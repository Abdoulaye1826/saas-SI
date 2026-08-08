<?php

namespace App\Services;

use App\Enums\OnlineOrderStatus;
use App\Enums\RoleSlug;
use App\Models\Customer;
use App\Models\OnlineOrder;
use App\Models\OnlineStoreSettings;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Commandes de la boutique en ligne.
 *
 * Règle centrale (voir plan Phase 2) : une commande reste indépendante de
 * `Sale` tant qu'elle n'est pas confirmée par un membre du staff — c'est
 * SEULEMENT `confirm()` qui appelle SaleService::create() (stock décrémenté,
 * facture générée via le pipeline existant). Aucune deuxième logique de
 * stock/facturation n'est créée ici.
 */
class OnlineOrderService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
        private readonly SaleService $saleService,
        private readonly ReturnService $returnService,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return OnlineOrder::query()
            ->with(['customer', 'assignedDriver'])
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Crée la commande à partir des lignes du panier (déjà résolues et
     * plafonnées au stock par CartService::items()) et des informations du
     * formulaire de checkout. Revalide le stock une dernière fois côté
     * serveur avant toute création.
     *
     * @param  Collection<int, array{product: Product, quantity: int, line_total: float}>  $cartItems
     * @param  Customer|null  $customer  Client déjà authentifié (Phase 3) —
     *   si fourni, utilisé directement au lieu de chercher/créer un
     *   Customer par téléphone (voir findOrCreateGuestCustomer()).
     */
    public function createFromCart(Collection $cartItems, array $data, ?Customer $customer = null): OnlineOrder
    {
        if ($cartItems->isEmpty()) {
            throw new \RuntimeException('Le panier est vide.');
        }

        foreach ($cartItems as $line) {
            /** @var Product $product */
            $product = $line['product'];

            if (!$product->show_on_store || !$product->is_active || !$product->allow_order) {
                throw new \RuntimeException("Le produit « {$product->name} » n'est plus disponible à la commande.");
            }

            if ($product->stock_quantity < $line['quantity']) {
                throw new \RuntimeException("Stock insuffisant pour « {$product->name} » ({$product->stock_quantity} disponible(s)).");
            }

            // Un IMEI = un appareil : impossible de commander plusieurs
            // unités d'un même produit suivi par IMEI en une seule ligne.
            if ($product->tracks_imei && $line['quantity'] > 1) {
                throw new \RuntimeException("« {$product->name} » ne peut être commandé qu'à l'unité.");
            }
        }

        return DB::transaction(function () use ($cartItems, $data, $customer) {
            $settings = OnlineStoreSettings::current();
            $subtotal = round((float) $cartItems->sum('line_total'), 2);

            $zone = $data['delivery_method'] === 'pickup' ? 'pickup' : ($data['delivery_zone'] ?? 'other');
            $deliveryFee = $settings->deliveryFeeFor($zone, $subtotal);

            $customer ??= $this->findOrCreateGuestCustomer($data);

            $order = OnlineOrder::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'status' => OnlineOrderStatus::New,
                'guest_name' => $data['guest_name'] ?? $customer->full_name,
                'guest_phone' => $data['guest_phone'] ?? $customer->phone,
                'guest_email' => $data['guest_email'] ?? $customer->email,
                'delivery_method' => $data['delivery_method'],
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_city' => $data['delivery_city'] ?? null,
                'delivery_zone' => $zone,
                'delivery_fee' => $deliveryFee,
                'payment_method' => 'cash_on_delivery',
                'subtotal' => $subtotal,
                'total' => round($subtotal + $deliveryFee, 2),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $line) {
                /** @var Product $product */
                $product = $line['product'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => $product->effective_price,
                    'line_total' => $line['line_total'],
                ]);
            }

            $this->activityLog->log('create', $order, "Commande en ligne créée : {$order->order_number}");

            return $order->fresh('items.product');
        });
    }

    /**
     * Confirme une commande "Nouvelle" : crée la Vente/Facture via le
     * pipeline existant (SaleService::create()), sélectionne automatiquement
     * un IMEI disponible pour les produits trackés (jamais exposé au
     * client), puis relie la commande à cette vente.
     */
    public function confirm(OnlineOrder $order, int $staffUserId): OnlineOrder
    {
        if ($order->status !== OnlineOrderStatus::New) {
            throw new \RuntimeException('Seule une commande "Nouvelle" peut être confirmée.');
        }

        $order->loadMissing('items.product');

        $productIds = [];
        $quantities = [];
        $unitPrices = [];
        $imeis = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product === null || !$product->is_active) {
                throw new \RuntimeException("Le produit « {$item->product_name} » n'existe plus ou a été désactivé.");
            }

            if ($product->tracks_imei) {
                $imei = $product->imeis()->available()->first();
                if ($imei === null) {
                    throw new \RuntimeException("Plus aucun IMEI disponible pour « {$product->name} ».");
                }
                $imeis[] = $imei->imei;
            } else {
                if ($product->stock_quantity < $item->quantity) {
                    throw new \RuntimeException("Stock insuffisant pour « {$product->name} » ({$product->stock_quantity} disponible(s)).");
                }
                $imeis[] = null;
            }

            $productIds[] = $product->id;
            $quantities[] = $item->quantity;
            $unitPrices[] = (float) $item->unit_price;
        }

        return DB::transaction(function () use ($order, $staffUserId, $productIds, $quantities, $unitPrices, $imeis) {
            $sale = $this->saleService->create([
                'customer_id' => $order->customer_id,
                'sale_type' => 'vente',
                'status' => 'validated',
                'discount_amount' => 0,
                'product_id' => $productIds,
                'quantity' => $quantities,
                'unit_price' => $unitPrices,
                'imei' => $imeis,
                'warranty_duration' => 'none',
                // Paiement à la livraison (COD) : aucun encaissement
                // automatique — le staff enregistre le paiement réel via la
                // facture générée une fois la commande livrée/payée.
                'payment_method' => null,
                'notes' => "Commande en ligne {$order->order_number}",
            ], $staffUserId);

            $order->update([
                'sale_id' => $sale->id,
                'status' => OnlineOrderStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            $this->activityLog->log('confirm', $order, "Commande en ligne confirmée : {$order->order_number} → vente {$sale->sale_number}");

            return $order->fresh(['sale.invoice', 'items.product']);
        });
    }

    /**
     * Fait avancer le statut de suivi (préparation/livraison), en dehors de
     * la confirmation et de l'annulation qui ont leurs propres méthodes.
     */
    public function updateStatus(OnlineOrder $order, OnlineOrderStatus $status, ?int $driverId = null): OnlineOrder
    {
        if (!in_array($status, $order->status->allowedNextStatuses(), true)) {
            throw new \RuntimeException("Impossible de passer de « {$order->status->label()} » à « {$status->label()} ».");
        }

        $data = ['status' => $status];

        if ($driverId !== null) {
            $data['assigned_driver_id'] = $driverId;
        }

        if ($status === OnlineOrderStatus::Shipped) {
            $data['shipped_at'] = now();
        } elseif ($status === OnlineOrderStatus::Delivered) {
            $data['delivered_at'] = now();
        }

        $order->update($data);

        $this->activityLog->log('update_status', $order, "Commande en ligne {$order->order_number} → {$status->label()}");

        return $order->fresh();
    }

    /**
     * Annule une commande. Si elle avait déjà été confirmée (liée à une
     * Vente validée), retourne chacune de ses lignes via le module Retours
     * existant (ReturnService::returnItem()) — remet le stock, recalcule la
     * facture — plutôt que de dupliquer cette logique.
     */
    public function cancel(OnlineOrder $order, int $userId): OnlineOrder
    {
        if ($order->isCancelled()) {
            throw new \RuntimeException('Cette commande est déjà annulée.');
        }

        if ($order->status === OnlineOrderStatus::Delivered) {
            throw new \RuntimeException('Impossible d\'annuler une commande déjà livrée.');
        }

        return DB::transaction(function () use ($order, $userId) {
            if ($order->sale_id !== null) {
                $sale = $order->sale()->with('items')->first();
                foreach ($sale?->items ?? [] as $item) {
                    if (!$item->isReturned()) {
                        $this->returnService->returnItem($item, $userId);
                    }
                }
            }

            $order->update([
                'status' => OnlineOrderStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            $this->activityLog->log('cancel', $order, "Commande en ligne annulée : {$order->order_number}");

            return $order->fresh();
        });
    }

    public function getDrivers()
    {
        return User::withRole(RoleSlug::Driver)->active()->orderBy('name')->get();
    }

    private function findOrCreateGuestCustomer(array $data): Customer
    {
        $customer = Customer::where('phone', $data['guest_phone'])->latest('id')->first();

        if ($customer !== null) {
            return $customer;
        }

        return Customer::create([
            'full_name' => $data['guest_name'],
            'type' => 'client',
            'phone' => $data['guest_phone'],
            'email' => $data['guest_email'] ?? null,
            'address' => $data['delivery_address'] ?? null,
            'city' => $data['delivery_city'] ?? null,
            'registered_at' => now()->toDateString(),
        ]);
    }

    /**
     * Numéro de commande continu (OC-000001, OC-000002, ...), jamais
     * réinitialisé — même principe que SaleService::generateSaleNumber().
     */
    private function generateOrderNumber(): string
    {
        $max = OnlineOrder::query()
            ->get(['order_number'])
            ->pluck('order_number')
            ->filter()
            ->map(function ($value) {
                preg_match('/(\d+)$/', $value, $matches);

                return isset($matches[1]) ? (int) $matches[1] : 0;
            })
            ->max();

        return sprintf('OC-%06d', ((int) $max) + 1);
    }
}
