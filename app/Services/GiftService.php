<?php

namespace App\Services;

use App\Enums\GiftStatus;
use App\Enums\ImeiStatus;
use App\Enums\StockMovementType;
use App\Models\Gift;
use App\Models\Product;
use App\Models\ProductImei;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Cadeaux / produits offerts : sortie de stock gratuite et traçable.
 *
 * Volontairement séparé de SaleService — un cadeau n'est jamais un Sale
 * (même à 0 FCFA) : aucune Invoice, aucun Payment, aucune TreasuryTransaction
 * n'est jamais créée depuis ce service, par construction plutôt que par un
 * filtre à maintenir. Voir la décision d'architecture dans le plan associé.
 */
class GiftService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Gift::query()
            ->with(['customer', 'user', 'items.product', 'items.productImei'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('gift_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('full_name', 'like', "%{$search}%"))
                        ->orWhereHas('items.product', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['customer_id'] ?? null, function ($query, $customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($filters['product_id'] ?? null, function ($query, $productId) {
                $query->whereHas('items', fn ($q) => $q->where('product_id', $productId));
            })
            ->when($filters['user_id'] ?? null, function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when($filters['date_from'] ?? null, function ($query, $date) {
                $query->whereDate('gift_date', '>=', $date);
            })
            ->when($filters['date_to'] ?? null, function ($query, $date) {
                $query->whereDate('gift_date', '<=', $date);
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Crée un cadeau : valide et résout chaque ligne (stock/IMEI disponible)
     * AVANT toute écriture, puis déduit le stock et journalise. Jamais de
     * clamp silencieux à 0 (contrairement à SaleService::applyStockChanges()
     * pour les produits non suivis par IMEI) : une ligne insuffisamment en
     * stock bloque toute l'opération (cahier §4).
     */
    public function create(array $data, int $userId): Gift
    {
        $items = $this->buildGiftItems($data);

        if (empty($items)) {
            throw new \RuntimeException('Veuillez sélectionner au moins un produit à offrir.');
        }

        return DB::transaction(function () use ($data, $items, $userId) {
            // Résolution + vérification de disponibilité de chaque ligne
            // avant la moindre écriture.
            $resolved = [];
            foreach ($items as $index => $itemData) {
                $product = Product::find($itemData['product_id']);
                if ($product === null) {
                    throw new \RuntimeException('Produit introuvable.');
                }

                if ($itemData['tracks_imei']) {
                    $resolved[$index] = ['product' => $product, 'imei' => $this->resolveAvailableImei($product, $itemData['imei'])];
                    continue;
                }

                if ($itemData['quantity'] > $product->stock_quantity) {
                    throw new \RuntimeException('Stock insuffisant pour offrir ce produit.');
                }
                $resolved[$index] = ['product' => $product, 'imei' => null];
            }

            $gift = Gift::create([
                'gift_number' => $this->generateGiftNumber(),
                'customer_id' => $data['customer_id'],
                'user_id' => $userId,
                'gift_date' => now(),
                'status' => GiftStatus::Given,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $index => $itemData) {
                $product = $resolved[$index]['product'];
                $imei = $resolved[$index]['imei'];

                $gift->items()->create([
                    'product_id' => $product->id,
                    'product_imei_id' => $imei?->id,
                    'quantity' => $itemData['quantity'],
                    // Valeur informative uniquement (cahier §6) — jamais un
                    // montant à payer.
                    'unit_value' => $product->sale_price,
                ]);

                if ($imei !== null) {
                    $this->offerImei($gift, $product, $imei);
                } else {
                    $this->offerQuantity($gift, $product, $itemData['quantity']);
                }
            }

            $this->activityLog->log('create', $gift, "Cadeau créé : {$gift->gift_number} pour {$gift->customer?->full_name}");

            // Aucun appel à InvoiceService/PaymentService/TreasuryService ici :
            // un cadeau ne génère jamais de facture, de paiement ni d'entrée
            // de trésorerie (cahier §6, §7, §11).

            return $gift->fresh('items.product', 'items.productImei');
        });
    }

    /**
     * Annule un cadeau (la « correction » du cahier §9) : remet le stock en
     * place et marque le cadeau comme annulé, sans jamais supprimer la ligne
     * — l'historique reste consultable.
     */
    public function cancel(Gift $gift, int $userId): Gift
    {
        if ($gift->isCancelled()) {
            throw new \RuntimeException('Ce cadeau a déjà été annulé.');
        }

        return DB::transaction(function () use ($gift, $userId) {
            $gift->loadMissing('items.product', 'items.productImei');

            foreach ($gift->items as $item) {
                $product = $item->product;
                if ($product === null) {
                    continue;
                }

                if ($item->productImei !== null) {
                    $this->restockImei($gift, $product, $item->productImei);
                } else {
                    $this->restockQuantity($gift, $product, $item->quantity);
                }
            }

            $gift->update(['status' => GiftStatus::Cancelled]);

            $this->activityLog->log('gift_cancel', $gift, "Cadeau annulé : {$gift->gift_number}");

            return $gift->fresh();
        });
    }

    /**
     * Numéro de cadeau continu (BC-000001, BC-000002, ...), jamais réutilisé
     * ni réinitialisé — même idiome que SaleService::generateSaleNumber().
     */
    public function generateGiftNumber(): string
    {
        $max = Gift::query()->get(['gift_number'])
            ->pluck('gift_number')
            ->filter()
            ->map(function ($value) {
                preg_match('/(\d+)$/', $value, $matches);

                return isset($matches[1]) ? (int) $matches[1] : 0;
            })
            ->max();

        return sprintf('BC-%06d', ((int) $max) + 1);
    }

    /**
     * Génère le PDF du bon de cadeau — même configuration DomPDF que
     * InvoiceService::renderPdfContent()/SaleService::renderExchangeVoucherPdfContent()
     * pour une cohérence visuelle avec les autres documents.
     */
    public function renderPdfContent(Gift $gift): string
    {
        $gift->load(['customer', 'user', 'items.product', 'items.productImei']);
        $isPdf = true;

        $pdf = PDF::loadView('documents.gift_voucher', compact('gift', 'isPdf'))
            ->setPaper([0, 0, 595.92, 842.88], 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultMediaType', 'print');

        return $pdf->output();
    }

    private function offerImei(Gift $gift, Product $product, ProductImei $imei): void
    {
        $quantityBefore = $product->stock_quantity;

        $imei->update([
            'status' => ImeiStatus::Offered->value,
            'gift_id' => $gift->id,
            'given_at' => now(),
        ]);
        $product->syncImeiStock();

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $gift->user_id,
            'type' => StockMovementType::Gift,
            'quantity' => 1,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $product->fresh()->stock_quantity,
            'reason' => "Cadeau / produit offert (IMEI {$imei->imei})",
            'reference' => $gift->gift_number,
        ]);
    }

    private function offerQuantity(Gift $gift, Product $product, int $quantity): void
    {
        $quantityBefore = $product->stock_quantity;
        // Déjà validé <= stock disponible avant l'appel : jamais de clamp
        // silencieux ici, la garde a eu lieu en amont dans create().
        $quantityAfter = $quantityBefore - $quantity;

        $product->update(['stock_quantity' => $quantityAfter]);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $gift->user_id,
            'type' => StockMovementType::Gift,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reason' => 'Cadeau / produit offert',
            'reference' => $gift->gift_number,
        ]);
    }

    private function restockImei(Gift $gift, Product $product, ProductImei $imei): void
    {
        $quantityBefore = $product->stock_quantity;

        $imei->update([
            'status' => ImeiStatus::Available->value,
            'gift_id' => null,
            'given_at' => null,
        ]);
        $product->syncImeiStock();

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $gift->user_id,
            'type' => StockMovementType::Return,
            'quantity' => 1,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $product->fresh()->stock_quantity,
            'reason' => "Annulation cadeau {$gift->gift_number} (IMEI {$imei->imei})",
            'reference' => $gift->gift_number,
        ]);
    }

    private function restockQuantity(Gift $gift, Product $product, int $quantity): void
    {
        $quantityBefore = $product->stock_quantity;
        $quantityAfter = $quantityBefore + $quantity;

        $product->update(['stock_quantity' => $quantityAfter]);

        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $gift->user_id,
            'type' => StockMovementType::Return,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reason' => "Annulation cadeau {$gift->gift_number}",
            'reference' => $gift->gift_number,
        ]);
    }

    /**
     * Résout l'IMEI saisi/scanné vers l'unité disponible correspondante.
     * Messages distincts pour une saisie manquante/inconnue (erreur de
     * saisie) et pour une unité non disponible (véritable rupture de stock,
     * texte exact du cahier §4).
     */
    private function resolveAvailableImei(Product $product, ?string $imeiValue): ProductImei
    {
        $imeiValue = trim((string) $imeiValue);

        if ($imeiValue === '') {
            throw new \RuntimeException('Veuillez saisir ou scanner un IMEI pour ce produit.');
        }

        $imei = ProductImei::where('product_id', $product->id)
            ->where('imei', $imeiValue)
            ->first();

        if ($imei === null) {
            throw new \RuntimeException("L'IMEI {$imeiValue} n'est pas enregistré pour ce produit.");
        }

        if ($imei->status !== ImeiStatus::Available) {
            throw new \RuntimeException('Stock insuffisant pour offrir ce produit.');
        }

        return $imei;
    }

    private function buildGiftItems(array $data): array
    {
        $productIds = Arr::wrap($data['product_id'] ?? []);
        $quantities = Arr::wrap($data['quantity'] ?? []);
        $imeis = Arr::wrap($data['imei'] ?? []);

        $items = [];
        foreach ($productIds as $index => $productId) {
            if (empty($productId)) {
                continue;
            }

            $product = Product::find($productId);
            $tracksImei = (bool) $product?->tracks_imei;

            $quantity = isset($quantities[$index]) ? (int) $quantities[$index] : 1;
            // Un IMEI = un appareil : la quantité est toujours 1 pour ces
            // produits (même règle que SaleService::buildSaleItems()).
            $quantity = $tracksImei ? 1 : max(1, $quantity);

            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'tracks_imei' => $tracksImei,
                'imei' => $tracksImei ? trim((string) ($imeis[$index] ?? '')) : null,
            ];
        }

        return $items;
    }
}
