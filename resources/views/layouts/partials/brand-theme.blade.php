{{-- Retheme la sidebar/les boutons avec la couleur de marque du client
     (§ admin/entreprise). Place cette balise APRÈS dashboard.css/forms-ui.css
     dans le <head> : à spécificité égale, l'ordre dans le document tranche,
     donc ce bloc doit être chargé en dernier pour l'emporter. --}}
<style>
    :root {
        --copper: {{ $entreprise->accent_color ?: \App\Models\Entreprise::DEFAULT_ACCENT_COLOR }};
        --copper-dark: {{ $entreprise->accent_color_dark }};
        --copper-soft: {{ $entreprise->accent_color_soft }};
        --sidebar-active: var(--copper);
        --primary: var(--copper);
    }

    .sidebar { background: var(--copper); }

    {{-- Bouton translucide de la page de connexion (.auth-card), qui utilise
         des rgba() codés en dur plutôt que var(--copper) dans dashboard.css. --}}
    .auth-card .btn-primary {
        background: rgba({{ $entreprise->accent_color_rgb }}, 0.55);
        box-shadow: 0 8px 24px rgba({{ $entreprise->accent_color_rgb }}, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }

    .auth-card .btn-primary:hover {
        background: rgba({{ $entreprise->accent_color_rgb }}, 0.7);
    }
</style>
