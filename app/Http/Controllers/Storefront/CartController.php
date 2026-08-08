<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreEvent;
use App\Services\CartService;
use App\Services\StoreAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly StoreAnalyticsService $analytics,
    ) {
    }

    public function show(): View
    {
        return view('storefront.cart.show', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $product = Product::onStore()->where('allow_order', true)->find($data['product_id']);

        if ($product === null) {
            return back()->with('error', "Ce produit n'est plus disponible à la commande.");
        }

        // Un IMEI = un appareil : jamais plus d'une unité par ligne pour un
        // produit suivi par IMEI (même règle que OnlineOrderService::createFromCart()).
        $quantity = $product->tracks_imei ? 1 : ($data['quantity'] ?? 1);

        $this->cart->add($product->id, $quantity);
        $this->analytics->track(StoreEvent::TYPE_CART_ADD, $product->id);

        return back()->with('success', "« {$product->name} » ajouté au panier.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:50'],
        ]);

        $this->cart->update($product->id, $product->tracks_imei ? min(1, $data['quantity']) : $data['quantity']);

        return back()->with('success', 'Panier mis à jour.');
    }

    public function remove(Product $product): RedirectResponse
    {
        $this->cart->remove($product->id);

        return back()->with('success', 'Produit retiré du panier.');
    }
}
