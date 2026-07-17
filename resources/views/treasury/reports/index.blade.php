@extends('layouts.dashboard')

@section('title', 'Rapports de trésorerie')
@section('page-title', 'Rapports de trésorerie')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-file-earmark-text me-2"></i>Rapports</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('treasury.dashboard') }}">Trésorerie</a></li>
        <li class="breadcrumb-item active">Rapports</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('treasury.reports.pdf', request()->only(['period', 'start', 'end'])) }}" class="btn btn-primary">
    <i class="bi bi-printer me-1"></i>Imprimer en PDF
  </a>
</div>

<div class="card border-0 shadow-sm mb-4 filter-card">
  <div class="card-body">
    <form method="GET" action="{{ route('treasury.reports.index') }}" class="row g-3 align-items-end">
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
          <div class="kpi-label">Total entrées — {{ $period->label }}</div>
          <div class="kpi-value">{{ number_format($report['entrees'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-danger text-danger"><i class="bi bi-arrow-up-circle"></i></div>
        <div>
          <div class="kpi-label">Total dépenses — {{ $period->label }}</div>
          <div class="kpi-value">{{ number_format($report['depenses'], 0, ',', ' ') }} FCFA</div>
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
          <div class="kpi-value">{{ number_format($report['solde'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Type</th>
          <th>Catégorie</th>
          <th>Description</th>
          <th class="text-end">Montant</th>
          <th>Utilisateur</th>
        </tr>
      </thead>
      <tbody>
        @forelse($report['transactions'] as $t)
          <tr>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td>
              @if($t->type->value === 'in')
                <span class="badge bg-success">Entrée</span>
              @else
                <span class="badge bg-danger">Sortie</span>
              @endif
            </td>
            <td>{{ $t->categoryLabel() }}</td>
            <td>{{ $t->description ?? '—' }}</td>
            <td class="text-end fw-semibold {{ $t->type->value === 'in' ? 'text-success' : 'text-danger' }}">
              {{ $t->type->value === 'in' ? '+' : '-' }}{{ number_format($t->amount, 0, ',', ' ') }} FCFA
            </td>
            <td>{{ $t->user->name ?? '—' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Aucune opération sur cette période.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
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
