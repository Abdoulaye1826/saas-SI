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

@include('dashboard.partials.filter-bar', ['period' => $period])

<div id="dashboardKpisWrapper" class="mb-2">
  @include('dashboard.partials.kpis', ['stats' => $stats, 'isCashier' => $isCashier])
</div>

{{-- Évolution des ventes + Évolution du chiffre d'affaires --}}
@unless($isCashier)
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="chart-card">
      <div class="card-title"><i class="bi bi-graph-up me-2"></i>Évolution des ventes</div>
      <canvas id="salesEvolutionChart" height="220"></canvas>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="chart-card">
      <div class="card-title"><i class="bi bi-bar-chart me-2"></i>Évolution du chiffre d'affaires</div>
      <canvas id="revenueEvolutionChart" height="220"></canvas>
    </div>
  </div>
</div>
@endunless

{{-- Répartition des ventes par catégorie + Statut des factures + Ventes vs Échanges --}}
<div class="row g-3 mb-4">
  <div class="col-lg-{{ $isCashier ? 12 : 4 }}">
    <div class="chart-card h-100">
      <div class="card-title"><i class="bi bi-pie-chart me-2"></i>Répartition des ventes</div>
      <canvas id="salesByCategoryChart" height="220"></canvas>
    </div>
  </div>
  @unless($isCashier)
  <div class="col-lg-4">
    <div class="chart-card h-100">
      <div class="card-title"><i class="bi bi-pie-chart-fill me-2"></i>Statut des factures</div>
      <canvas id="invoiceStatusChart" height="220"></canvas>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="chart-card h-100">
      <div class="card-title"><i class="bi bi-arrow-left-right me-2"></i>Ventes vs Échanges</div>
      <canvas id="salesTypeChart" height="220"></canvas>
    </div>
  </div>
  @endunless
</div>

<div id="dashboardTablesWrapper">
  @include('dashboard.partials.tables', [
    'isCashier' => $isCashier,
    'recentStockMovements' => $recentStockMovements,
    'recentQuotes' => $recentQuotes,
    'recentInvoices' => $recentInvoices,
    'topCustomers' => $topCustomers,
    'salesByUser' => $salesByUser,
    'topProducts' => $topProducts,
    'stockAlerts' => $stockAlerts,
  ])
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
(function () {
  const isCashier = @json($isCashier);
  const dashboardUrl = @json(route('dashboard'));
  const chartDefaults = { responsive: true, maintainAspectRatio: true };
  const categoryColors = ['#1e3a5f', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899'];

  let charts = {};
  let inFlightController = null;

  function destroyCharts() {
    Object.values(charts).forEach(chart => chart?.destroy());
    charts = {};
  }

  function renderCharts(data) {
    destroyCharts();

    if (!isCashier) {
      const evo = data.salesEvolution;
      charts.salesEvolution = new Chart(document.getElementById('salesEvolutionChart'), {
        type: 'line',
        data: {
          labels: evo.labels,
          datasets: [{
            label: 'Ventes',
            data: evo.count,
            borderColor: '#1e3a5f',
            backgroundColor: 'rgba(30, 58, 95, 0.12)',
            fill: true,
            tension: 0.35,
            pointRadius: 2,
            pointHoverRadius: 5,
            borderWidth: 2,
          }]
        },
        options: {
          ...chartDefaults,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }
          }
        }
      });

      charts.revenueEvolution = new Chart(document.getElementById('revenueEvolutionChart'), {
        type: 'bar',
        data: {
          labels: evo.labels,
          datasets: [{
            label: 'CA (FCFA)',
            data: evo.revenue,
            backgroundColor: 'rgba(20, 40, 63, 0.75)',
            borderRadius: 6,
          }]
        },
        options: {
          ...chartDefaults,
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA' } }
          },
          scales: { y: { beginAtZero: true } }
        }
      });
    }

    const catLabels = data.salesByCategory.labels;
    const catData = data.salesByCategory.data;
    charts.salesByCategory = new Chart(document.getElementById('salesByCategoryChart'), {
      type: 'doughnut',
      data: {
        labels: catLabels.length ? catLabels : ['Aucune donnée'],
        datasets: [{
          data: catData.length ? catData : [1],
          backgroundColor: catLabels.length ? categoryColors.slice(0, catLabels.length) : ['#e2e8f0'],
        }]
      },
      options: {
        ...chartDefaults,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
      }
    });

    if (!isCashier) {
      const invoiceLabels = data.invoiceStatusSummary.labels;
      const invoiceValues = data.invoiceStatusSummary.values;
      const invoiceColors = ['#1e3a5f', '#198754', '#ffc107', '#dc3545'];
      charts.invoiceStatus = new Chart(document.getElementById('invoiceStatusChart'), {
        type: 'doughnut',
        data: {
          labels: invoiceLabels.length ? invoiceLabels : ['Aucune donnée'],
          datasets: [{
            data: invoiceValues.length ? invoiceValues : [1],
            backgroundColor: invoiceLabels.length ? invoiceColors.slice(0, invoiceLabels.length) : ['#e2e8f0'],
          }]
        },
        options: {
          ...chartDefaults,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
        }
      });

      const typeLabels = data.salesTypeBreakdown.labels;
      const typeData = data.salesTypeBreakdown.data;
      charts.salesType = new Chart(document.getElementById('salesTypeChart'), {
        type: 'doughnut',
        data: {
          labels: typeLabels,
          datasets: [{
            data: typeData.some(v => v > 0) ? typeData : [1, 0],
            backgroundColor: ['#1e3a5f', '#fd7e14'],
          }]
        },
        options: {
          ...chartDefaults,
          plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
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
    salesByCategory: @json($salesByCategory),
    invoiceStatusSummary: @json($invoiceStatusSummary ?? ['labels' => [], 'values' => [], 'counts' => []]),
    salesTypeBreakdown: @json($salesTypeBreakdown ?? ['labels' => [], 'data' => []]),
  });
})();
</script>
@endpush
