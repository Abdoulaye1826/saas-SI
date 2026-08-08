<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class CustomerPasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('storefront.auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Un compte sans email (créé via checkout invité/staff sans email
        // renseigné) ne peut pas recevoir de lien — message clair plutôt
        // qu'une erreur serveur ou un faux positif "lien envoyé".
        $customer = Customer::where('email', $request->input('email'))->first();

        if ($customer === null || ! $customer->hasAccount()) {
            return back()->withInput()->withErrors([
                'email' => "Aucun compte actif n'est associé à cette adresse email.",
            ]);
        }

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
