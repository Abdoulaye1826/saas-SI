<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Panier de la boutique en ligne, stocké en session (pas de compte client
 * pour l'instant — voir Phase 3). Seule la quantité par produit est
 * persistée (`[product_id => quantity]`) : les prix/stock affichés sont
 * toujours relus depuis Product au moment de l'affichage, jamais dupliqués
 * dans la session.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$productId] = ($cart[$productId] ?? 0) + max(1, $quantity);
        $this->save($cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return empty($this->raw());
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Lignes du panier avec le Product actuel rechargé (prix/stock à jour).
     * Une ligne dont le produit a été retiré de la boutique entre-temps
     * (dépublié, désactivé, supprimé) est simplement omise plutôt que de
     * planter l'affichage — elle reste comptée nulle part ensuite.
     *
     * @return Collection<int, array{product: Product, quantity: int, line_total: float}>
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', array_keys($cart))
            ->onStore()
            ->get()
            ->filter(fn (Product $product) => $product->allow_order)
            ->map(function (Product $product) use ($cart) {
                // La quantité demandée est plafonnée au stock réellement
                // disponible (ex: stock diminué par une vente en magasin
                // depuis l'ajout au panier) — jamais au-delà.
                $quantity = min($cart[$product->id], max(0, $product->stock_quantity));

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'line_total' => round($quantity * $product->effective_price, 2),
                ];
            })
            ->filter(fn (array $line) => $line['quantity'] > 0)
            ->values();
    }

    public function subtotal(): float
    {
        return round((float) $this->items()->sum('line_total'), 2);
    }

    private function raw(): array
    {
        return array_map('intval', Session::get(self::SESSION_KEY, []));
    }

    private function save(array $cart): void
    {
        Session::put(self::SESSION_KEY, $cart);
    }
}
