@extends('layouts.dashboard')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1>Tableau de bord</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active">Accueil</li>
      </ol>
    </nav>
  </div>
  <div class="text-muted small">
    <i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l d F Y') }}
  </div>
</div>

<div class="dashboard-hero mb-2">
  <div id="dashboardKpisWrapper">
    @include('dashboard.partials.kpis', ['stats' => $stats, 'isCashier' => $isCashier, 'period' => $period])
  </div>

  @include('dashboard.partials.filter-bar', ['period' => $period])
</div>

{{-- Actions rapides --}}
<div class="quick-actions mb-4">
  <a href="{{ route('sales.create') }}" class="quick-action-btn quick-action-btn--primary">
    <i class="bi bi-cart-plus"></i>
    <span>Nouvelle vente</span>
  </a>
  <a href="{{ route('invoices.create') }}" class="quick-action-btn quick-action-btn--info">
    <i class="bi bi-receipt"></i>
    <span>Nouvelle facture</span>
  </a>
  <a href="{{ route('quotes.create') }}" class="quick-action-btn quick-action-btn--warning">
    <i class="bi bi-file-earmark-ruled"></i>
    <span>Nouveau devis</span>
  </a>
  <a href="{{ route('customers.create') }}" class="quick-action-btn quick-action-btn--success">
    <i class="bi bi-person-plus"></i>
    <span>Nouveau client</span>
  </a>
  <a href="{{ route('treasury.expenses.create') }}" class="quick-action-btn quick-action-btn--danger">
    <i class="bi bi-dash-circle"></i>
    <span>Dépense directe</span>
  </a>
</div>

{{-- Évolution du chiffre d'affaires — taille réduite, au-dessus des
     tableaux ; le détail complet (ventes par catégorie, ventes vs
     échanges, vendeurs performants, produits les plus vendus, statut des
     factures, CA par mois, top clients, devis récents, mouvements de
     stock) est sur la page Rapports. --}}
@unless($isCashier)
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="chart-card">
      <div class="card-title"><i class="bi bi-graph-up me-2"></i>Évolution du chiffre d'affaires</div>
      <canvas id="revenueEvolutionChart" height="55"></canvas>
    </div>
  </div>
</div>
@endunless

<div id="dashboardTablesWrapper">
  @include('dashboard.partials.tables', [
    'isCashier' => $isCashier,
    'recentInvoices' => $recentInvoices,
    'stockAlerts' => $stockAlerts,
  ])
</div>
@endsection

@push('styles')
<style>
    /* ── Filtre de période intégré au KPI principal ────────────────────
       .hero-period-control est un frère de #dashboardKpisWrapper (jamais
       remplacé par l'AJAX), positionné en absolu par-dessus le coin
       supérieur droit de .kpi-card--hero grâce à .dashboard-hero en
       position: relative. */
    .dashboard-hero { position: relative; }

    .hero-period-control {
        position: absolute;
        top: 1.5rem;
        right: 2rem;
        z-index: 3;
    }

    .hero-period-select {
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .35);
        color: #fff;
        border-radius: 8px;
        padding: .4rem 1.75rem .4rem .75rem;
        font-size: .8rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        cursor: pointer;
    }

    .hero-period-select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
    }

    /* Le menu déroulant natif reste blanc : le texte des options doit
       rester lisible dessus, indépendamment de la couleur de marque. */
    .hero-period-select option { color: #1a1a2e; }

    .hero-period-popover {
        position: absolute;
        top: calc(100% + .5rem);
        right: 0;
        background: var(--card);
        border-radius: 10px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .18);
        padding: 1rem;
        width: 230px;
        z-index: 4;
    }

    @media (max-width: 575.98px) {
        .hero-period-control { position: static; margin-top: .75rem; display: block; }
        .hero-period-select { width: 100%; }
        .hero-period-popover { right: auto; left: 0; width: 100%; }
    }

    /* Libellé de la période affichée sous "Chiffre d'affaires" (ex:
       "Ce mois-ci"), mis à jour par le même JS que le select puisqu'il
       est recherché par id à chaque rafraîchissement AJAX. */
    .kpi-hero__period {
        display: block;
        font-size: .7rem;
        font-weight: 500;
        text-transform: none;
        letter-spacing: normal;
        color: rgba(255, 255, 255, .65);
        margin-top: .2rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
  const isCashier = @json($isCashier);
  const dashboardUrl = @json(route('dashboard'));
  const chartDefaults = { responsive: true, maintainAspectRatio: true };

  let charts = {};
  let inFlightController = null;

  function destroyCharts() {
    Object.values(charts).forEach(chart => chart?.destroy());
    charts = {};
  }

  function renderCharts(data) {
    destroyCharts();

    if (!isCashier) {
      // On ne garde que les 15 derniers points (jours) plutôt que tout le
      // mois, pour un graphique plus lisible sur le tableau de bord.
      const evoLabels = data.salesEvolution.labels.slice(-15);
      const evoRevenue = data.salesEvolution.revenue.slice(-15);
      charts.revenueEvolution = new Chart(document.getElementById('revenueEvolutionChart'), {
        type: 'line',
        data: {
          labels: evoLabels,
          datasets: [{
            label: 'CA (FCFA)',
            data: evoRevenue,
            borderColor: @json($entreprise->accent_color ?: '#0A1C73'),
            backgroundColor: @json($entreprise->accent_color_soft),
            fill: true,
            tension: 0.35,
            pointRadius: 2,
            pointHoverRadius: 5,
            borderWidth: 2,
          }]
        },
        options: {
          ...chartDefaults,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA' } }
          },
          scales: {
            y: { beginAtZero: true },
            x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }
          }
        }
      });
    }
  }

  function fetchDashboard(params) {
    if (inFlightController) {
      inFlightController.abort();
    }
    inFlightController = new AbortController();

    const url = dashboardUrl + '?' + params.toString();

    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      signal: inFlightController.signal,
    })
      .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
      })
      .then(json => {
        document.getElementById('dashboardKpisWrapper').innerHTML = json.kpisHtml;
        document.getElementById('dashboardTablesWrapper').innerHTML = json.tablesHtml;
        renderCharts(json.charts);
        document.getElementById('periodLabel').querySelector('span').textContent = json.period.label;
        window.history.pushState(null, '', url);
      })
      .catch(err => {
        if (err.name !== 'AbortError') console.error('Erreur chargement tableau de bord:', err);
      })
      .finally(() => { inFlightController = null; });
  }

  const periodSelect = document.getElementById('periodSelect');
  const customFields = document.getElementById('customPeriodFields');
  const periodStart = document.getElementById('periodStart');
  const periodEnd = document.getElementById('periodEnd');
  const applyCustomBtn = document.getElementById('applyCustomPeriod');

  periodSelect.addEventListener('change', function () {
    if (this.value === 'custom') {
      customFields.classList.remove('d-none');
      return;
    }

    customFields.classList.add('d-none');
    fetchDashboard(new URLSearchParams({ period: this.value }));
  });

  applyCustomBtn?.addEventListener('click', function () {
    if (!periodStart.value || !periodEnd.value) return;

    fetchDashboard(new URLSearchParams({
      period: 'custom',
      start: periodStart.value,
      end: periodEnd.value,
    }));
  });

  // Premier rendu : pas de round-trip AJAX nécessaire, les données sont
  // déjà injectées côté serveur.
  renderCharts({
    salesEvolution: @json($salesEvolution ?? ['labels' => [], 'revenue' => [], 'count' => []]),
  });
})();
</script>
@endpush
