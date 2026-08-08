<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protège les pages de l'espace client (guard `customer`), distinct du
 * middleware `auth` staff — celui-ci redirige en dur vers la connexion
 * staff (route('login')), inutilisable ici.
 */
class AuthenticateCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer')->check()) {
            if ($request->expectsJson()) {
                abort(401);
            }

            return redirect()->guest(route('store.account.login'));
        }

        Auth::shouldUse('customer');

        return $next($request);
    }
}
