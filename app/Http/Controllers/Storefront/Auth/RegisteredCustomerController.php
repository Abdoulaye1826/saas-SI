<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\RegisterCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredCustomerController extends Controller
{
    public function create(): View
    {
        return view('storefront.auth.register');
    }

    /**
     * Si un Customer existe déjà avec ce téléphone (créé par le staff, ou
     * par un checkout invité — voir OnlineOrderService::findOrCreateGuestCustomer())
     * et n'a pas encore de mot de passe, l'inscription "réclame" cette fiche
     * au lieu d'en créer une nouvelle : l'historique de commandes déjà
     * passées en invité apparaît immédiatement dans "Mes commandes".
     */
    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $existing = Customer::where('phone', $data['phone'])->first();

        if ($existing !== null && $existing->hasAccount()) {
            return back()->withInput()->withErrors([
                'phone' => 'Un compte existe déjà avec ce numéro de téléphone. Connectez-vous plutôt.',
            ]);
        }

        if ($existing !== null) {
            $existing->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'] ?? $existing->email,
                'password' => Hash::make($data['password']),
            ]);
            $customer = $existing;
        } else {
            $customer = Customer::create([
                'full_name' => $data['full_name'],
                'type' => 'client',
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'registered_at' => now()->toDateString(),
            ]);
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('store.account.orders.index')
            ->with('success', 'Bienvenue ! Votre compte est créé.');
    }
}
