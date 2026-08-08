<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreCheckoutRequest;
use App\Models\OnlineOrder;
use App\Models\OnlineStoreSettings;
use App\Services\CartService;
use App\Services\OnlineOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OnlineOrderService $onlineOrderService,
    ) {
    }

    public function show(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('store.cart.show')->with('error', 'Votre panier est vide.');
        }

        return view('storefront.checkout.show', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'settings' => OnlineStoreSettings::current(),
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('store.cart.show')->with('error', 'Votre panier est vide.');
        }

        try {
            $order = $this->onlineOrderService->createFromCart(
                $this->cart->items(),
                $request->validated(),
                Auth::guard('customer')->user()
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('store.cart.show')->with('error', $e->getMessage());
        }

        $this->cart->clear();

        // Lien signé (comme invoices.public-pdf / quotes.public-pdf) : le
        // numéro de commande est prévisible (OC-000001, OC-000002...) et ne
        // doit jamais, à lui seul, permettre à un tiers d'ouvrir la page de
        // confirmation d'un autre client (nom, téléphone, adresse).
        return redirect()->to(URL::temporarySignedRoute(
            'store.checkout.confirmation',
            now()->addDay(),
            ['order' => $order->id]
        ));
    }

    public function confirmation(OnlineOrder $order): View
    {
        $order->load('items.product');

        return view('storefront.checkout.confirmation', compact('order'));
    }
}
