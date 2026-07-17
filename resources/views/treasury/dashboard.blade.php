@extends('layouts.dashboard')

@section('title', 'Trésorerie')
@section('page-title', 'Trésorerie')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-cash-coin me-2"></i>Trésorerie</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Trésorerie</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('treasury.expenses.create') }}" class="btn btn-primary">
    <i class="bi bi-dash-circle me-1"></i>Nouvelle dépense
  </a>
</div>

<div class="row g-3 mb-3">
  <div class="col-12">
    <div class="kpi-card kpi-card--hero">
      <div class="kpi-hero__icon"><i class="bi bi-wallet2"></i></div>
      <div>
        <div class="kpi-hero__label">Solde actuel</div>
        <div class="kpi-hero__value">{{ number_format($stats['solde_actuel'], 0, ',', ' ') }} <span>FCFA</span></div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-4 filter-card">
  <div class="card-body">
    <form method="GET" action="{{ route('treasury.dashboard') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Période</label>
        <select name="period" id="periodSelect" class="form-control">
          <option value="today" @selected($period->key === 'today')>Aujourd'hui</option>
          <option value="yesterday" @selected($period->key === 'yesterday')>Hier</option>
          <option value="month" @selected($period->key === 'month')>Ce mois</option>
          <option value="year" @selected($period->key === 'year')>Cette année</option>
          <option value="custom" @selected($period->key === 'custom')>Période personnalisée</option>
        </select>
      </div>
      <div class="col-md-3 custom-period-field {{ $period->key !== 'custom' ? 'd-none' : '' }}">
        <label class="form-label small">Date de début</label>
        <input type="date" name="start" class="form-control" value="{{ $period->key === 'custom' ? $period->start->toDateString() : '' }}">
      </div>
      <div class="col-md-3 custom-period-field {{ $period->key !== 'custom' ? 'd-none' : '' }}">
        <label class="form-label small">Date de fin</label>
        <input type="date" name="end" class="form-control" value="{{ $period->key === 'custom' ? $period->end->toDateString() : '' }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrer</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-success text-success"><i class="bi bi-arrow-down-circle"></i></div>
        <div>
          <div class="kpi-label">Entrées — {{ $period->label }}</div>
          <div class="kpi-value">{{ number_format($stats['entrees_periode'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-danger text-danger"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
          <div class="kpi-label">Dépenses — {{ $period->label }}</div>
          <div class="kpi-value">{{ number_format($stats['depenses_periode'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-info text-info"><i class="bi bi-graph-up-arrow"></i></div>
        <div>
          <div class="kpi-label">Solde — {{ $period->label }}</div>
          <div class="kpi-value">{{ number_format($stats['solde_periode'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="table-card mb-4">
  <div class="p-3 border-bottom">
    <h2 class="h6 mb-0"><i class="bi bi-calendar-month me-2"></i>Ce mois-ci (référence fixe)</h2>
  </div>
  <div class="row g-3 p-3">
    <div class="col-md-4">
      <div class="kpi-label">Entrées du mois</div>
      <div class="kpi-value text-success">{{ number_format($stats['entrees_mois'], 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="col-md-4">
      <div class="kpi-label">Dépenses du mois</div>
      <div class="kpi-value text-danger">{{ number_format($stats['depenses_mois'], 0, ',', ' ') }} FCFA</div>
    </div>
    <div class="col-md-4">
      <div class="kpi-label">Solde du mois</div>
      <div class="kpi-value">{{ number_format($stats['solde_mois'], 0, ',', ' ') }} FCFA</div>
    </div>
  </div>
</div>

<div class="quick-actions mb-4">
  <a href="{{ route('treasury.expenses.create') }}" class="quick-action-btn quick-action-btn--primary">
    <i class="bi bi-dash-circle"></i>
    <span>Nouvelle dépense</span>
  </a>
  <a href="{{ route('treasury.history.index') }}" class="quick-action-btn quick-action-btn--info">
    <i class="bi bi-clock-history"></i>
    <span>Historique</span>
  </a>
  <a href="{{ route('treasury.reports.index') }}" class="quick-action-btn quick-action-btn--success">
    <i class="bi bi-file-earmark-text"></i>
    <span>Rapports</span>
  </a>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('periodSelect').addEventListener('change', function () {
  document.querySelectorAll('.custom-period-field').forEach(function (el) {
    el.classList.toggle('d-none', this.value !== 'custom');
  }, this);
});
</script>
@endpush
