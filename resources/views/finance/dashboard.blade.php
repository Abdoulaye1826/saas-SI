@extends('layouts.dashboard')

@section('title', 'Tableau de bord financier')
@section('page-title', 'Tableau de bord financier')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-cash-stack me-2"></i>Tableau de bord financier</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Finance</li>
      </ol>
    </nav>
  </div>
</div>

<div class="card border-0 shadow-sm filter-card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('finance.dashboard') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">Période</label>
        <select name="period" class="form-select" onchange="document.getElementById('customDateFields').classList.toggle('d-none', this.value !== 'custom'); if (this.value !== 'custom') this.form.submit();">
          <option value="today" @selected($period->key === 'today')>Aujourd'hui</option>
          <option value="yesterday" @selected($period->key === 'yesterday')>Hier</option>
          <option value="week" @selected($period->key === 'week')>Cette semaine</option>
          <option value="month" @selected($period->key === 'month')>Ce mois</option>
          <option value="year" @selected($period->key === 'year')>Cette année</option>
          <option value="custom" @selected($period->key === 'custom')>Période personnalisée</option>
        </select>
      </div>
      <div id="customDateFields" class="col-md-6 d-flex gap-2 {{ $period->key === 'custom' ? '' : 'd-none' }}">
        <div class="flex-fill">
          <label class="form-label small">Du</label>
          <input type="date" name="start" class="form-control" value="{{ $period->key === 'custom' ? $period->start->toDateString() : '' }}">
        </div>
        <div class="flex-fill">
          <label class="form-label small">Au</label>
          <input type="date" name="end" class="form-control" value="{{ $period->key === 'custom' ? $period->end->toDateString() : '' }}">
        </div>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Appliquer</button>
      </div>
    </form>
  </div>
</div>

{{-- Solde global --}}
<div class="row g-3 mb-3">
  <div class="col-12">
    <div class="kpi-card kpi-card--hero">
      <div class="kpi-hero__icon"><i class="bi bi-wallet2"></i></div>
      <div>
        <div class="kpi-hero__label">Solde global</div>
        <div class="kpi-hero__value">{{ number_format($kpis['global_balance'], 0, ',', ' ') }} <span>FCFA</span></div>
      </div>
    </div>
  </div>
</div>

{{-- Solde par compte --}}
<div class="row g-3 mb-3">
  @forelse($kpis['accounts'] as $account)
    <div class="col-6 col-md-3">
      <div class="kpi-card">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon bg-primary text-primary"><i class="bi {{ $account->type->icon() }}"></i></div>
          <div>
            <div class="kpi-label">{{ $account->name }}</div>
            <div class="kpi-value">{{ number_format((float) $account->current_balance, 0, ',', ' ') }} FCFA</div>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12 text-muted small">Aucun compte financier actif — <a href="{{ route('finance.accounts.create') }}">en créer un</a>.</div>
  @endforelse
</div>

{{-- Entrées / sorties — repères fixes jour, mois --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-success text-success"><i class="bi bi-arrow-down-circle"></i></div>
        <div><div class="kpi-label">Entrées du jour</div><div class="kpi-value">{{ number_format($kpis['today_entries'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-danger text-danger"><i class="bi bi-arrow-up-circle"></i></div>
        <div><div class="kpi-label">Sorties du jour</div><div class="kpi-value">{{ number_format($kpis['today_exits'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-success text-success"><i class="bi bi-arrow-down-circle"></i></div>
        <div><div class="kpi-label">Entrées du mois</div><div class="kpi-value">{{ number_format($kpis['month_entries'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-danger text-danger"><i class="bi bi-arrow-up-circle"></i></div>
        <div><div class="kpi-label">Sorties du mois</div><div class="kpi-value">{{ number_format($kpis['month_exits'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
</div>

{{-- Bénéfices --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-primary text-primary"><i class="bi bi-graph-up-arrow"></i></div>
        <div><div class="kpi-label">Bénéfice du jour</div><div class="kpi-value">{{ number_format($kpis['today_entries'] - $kpis['today_exits'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-primary text-primary"><i class="bi bi-graph-up-arrow"></i></div>
        <div><div class="kpi-label">Bénéfice du mois</div><div class="kpi-value">{{ number_format($kpis['month_entries'] - $kpis['month_exits'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-primary text-primary"><i class="bi bi-graph-up-arrow"></i></div>
        <div><div class="kpi-label">Bénéfice annuel</div><div class="kpi-value">{{ number_format($kpis['year_entries'] - $kpis['year_exits'], 0, ',', ' ') }} FCFA</div></div>
      </div>
    </div>
  </div>
</div>

{{-- Graphiques --}}
<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="chart-card">
      <h6 class="card-title"><i class="bi bi-graph-up me-2"></i>Évolution de la trésorerie</h6>
      <canvas id="treasuryChart" height="220"></canvas>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="chart-card">
      <h6 class="card-title"><i class="bi bi-bar-chart me-2"></i>Recettes vs dépenses</h6>
      <canvas id="revenueExpenseChart" height="220"></canvas>
    </div>
  </div>
</div>
<div class="row g-3">
  <div class="col-lg-6">
    <div class="chart-card">
      <h6 class="card-title"><i class="bi bi-pie-chart me-2"></i>Répartition des dépenses par catégorie</h6>
      <canvas id="expenseCategoryChart" height="220"></canvas>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="chart-card">
      <h6 class="card-title"><i class="bi bi-pie-chart-fill me-2"></i>Répartition des recettes par catégorie</h6>
      <canvas id="revenueCategoryChart" height="220"></canvas>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  const copper = getComputedStyle(document.documentElement).getPropertyValue('--copper').trim() || '#153BFF';
  const success = getComputedStyle(document.documentElement).getPropertyValue('--success').trim() || '#15803d';
  const danger = getComputedStyle(document.documentElement).getPropertyValue('--danger').trim() || '#b91c1c';
  const labels = @json($charts['labels']);

  new Chart(document.getElementById('treasuryChart'), {
    type: 'line',
    data: { labels, datasets: [{ label: 'Trésorerie nette', data: @json($charts['treasury_evolution']), borderColor: copper, backgroundColor: 'transparent', tension: .3 }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });

  new Chart(document.getElementById('revenueExpenseChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { label: 'Recettes', data: @json($charts['revenue_evolution']), backgroundColor: success },
        { label: 'Dépenses', data: @json($charts['expense_evolution']), backgroundColor: danger },
      ]
    },
    options: { responsive: true }
  });

  new Chart(document.getElementById('expenseCategoryChart'), {
    type: 'doughnut',
    data: { labels: @json($charts['expense_by_category']['labels']), datasets: [{ data: @json($charts['expense_by_category']['data']) }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
  });

  new Chart(document.getElementById('revenueCategoryChart'), {
    type: 'doughnut',
    data: { labels: @json($charts['revenue_by_category']['labels']), datasets: [{ data: @json($charts['revenue_by_category']['data']) }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
  });
</script>
@endpush
