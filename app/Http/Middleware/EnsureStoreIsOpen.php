<?php

namespace App\Http\Middleware;

use App\Models\OnlineStoreSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque tout accès à la boutique publique tant que son statut n'est pas
 * "active" (désactivée ou fermeture temporaire) : affiche la page
 * "Notre boutique est temporairement indisponible." à la place de la vitrine.
 */
class EnsureStoreIsOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = OnlineStoreSettings::current();

        if (! $settings->isOpen()) {
            return response()->view('storefront.closed', compact('settings'), 503);
        }

        return $next($request);
    }
}
