<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empêche un client déjà connecté de revoir les pages inscription/connexion
 * — équivalent du middleware `guest` staff, mais pour le guard `customer`
 * (RedirectIfAuthenticated redirige en dur vers /dashboard, la zone staff).
 */
class RedirectIfCustomerAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('store.account.orders.index');
        }

        return $next($request);
    }
}
