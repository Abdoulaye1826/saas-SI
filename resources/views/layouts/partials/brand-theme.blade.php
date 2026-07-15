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

        {{-- La sidebar a sa propre palette "Encre" (--ink-line pour les
             bordures/la scrollbar, --ink-soft pour le survol des liens),
             distincte de --copper dans dashboard.css. Sans ce recalage, la
             sidebar se retteinte mais sa scrollbar et ses bordures restent
             bleu marine. --}}
        --ink-line: var(--copper-dark);
        --ink-soft: var(--copper-dark);

        {{-- Couleur secondaire : réutilise le rôle sémantique "info"
             (carte KPI, bouton rapide, badges), déjà séparé du reste des
             couleurs de statut (succès/alerte/danger, qui doivent rester
             vert/orange/rouge quoi qu'il arrive). --}}
        --circuit-blue: {{ $entreprise->secondary_color ?: \App\Models\Entreprise::DEFAULT_SECONDARY_COLOR }};
        --blue-soft: {{ $entreprise->secondary_color_soft }};
    }

    .sidebar { background: var(--copper); }

    {{-- KPI principal (chiffre d'affaires) du tableau de bord : dégradé et
         icône codés en dur dans dashboard.css, indépendants de --copper.
         Dégradé primaire → secondaire pour donner sa place aux deux
         couleurs choisies. --}}
    .kpi-card--hero {
        background: linear-gradient(135deg, var(--copper) 0%, {{ $entreprise->secondary_color ?: \App\Models\Entreprise::DEFAULT_SECONDARY_COLOR }} 100%);
    }

    .kpi-card--hero .kpi-hero__icon {
        background: rgba({{ $entreprise->accent_color_rgb }}, .18);
        color: {{ $entreprise->accent_color_light }};
    }

    {{-- Lien de menu actif dans la sidebar : mis en évidence avec un
         dégradé en couleur secondaire plutôt que le fond plat en couleur
         primaire de dashboard.css. --}}
    .sidebar-nav .nav-link.active {
        background: linear-gradient(90deg, rgba({{ $entreprise->secondary_color_rgb }}, .9) 0%, rgba({{ $entreprise->secondary_color_rgb }}, .15) 100%);
        border-left-color: {{ $entreprise->secondary_color ?: \App\Models\Entreprise::DEFAULT_SECONDARY_COLOR }};
        color: #fff;
    }

    .sidebar-nav .nav-link.active i { color: #fff; }
</style>
