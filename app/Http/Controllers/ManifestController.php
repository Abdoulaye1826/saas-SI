<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;

/**
 * Manifeste web dynamique : permet à un client d'« Ajouter à l'écran
 * d'accueil » (mobile) ou d'installer l'app (Chrome desktop) avec le nom
 * et le logo réels de son entreprise, plutôt qu'une icône générique.
 */
class ManifestController extends Controller
{
    public function index(): JsonResponse
    {
        $entreprise = Entreprise::current();

        $icons = [];
        if ($entreprise->logo_url) {
            $icons[] = [
                'src' => $entreprise->logo_url,
                'sizes' => 'any',
                'type' => $entreprise->logo_mime ?: 'image/png',
            ];
        }

        return response()->json([
            'name' => $entreprise->name,
            'short_name' => str($entreprise->name)->limit(12, ''),
            'start_url' => route('dashboard'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $entreprise->accent_color ?: Entreprise::DEFAULT_ACCENT_COLOR,
            'icons' => $icons,
        ])->header('Content-Type', 'application/manifest+json');
    }
}
