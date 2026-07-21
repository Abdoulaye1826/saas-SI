@extends('layouts.dashboard')

@section('title', 'Historique de trésorerie')
@section('page-title', 'Historique de trésorerie')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-clock-history me-2"></i>Historique</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('treasury.dashboard') }}">Trésorerie</a></li>
        <li class="breadcrumb-item active">Historique</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('treasury.expenses.create') }}" class="btn btn-primary">
    <i class="bi bi-dash-circle me-1"></i>Nouvelle dépense
  </a>
</div>

<div class="card border-0 shadow-sm mb-4 filter-card">
  <div class="card-body">
    <form method="GET" action="{{ route('treasury.history.index') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">Type</label>
        <select name="type" class="form-control">
          <option value="">Tous</option>
          <option value="in" @selected(($filters['type'] ?? '') === 'in')>Entrée</option>
          <option value="out" @selected(($filters['type'] ?? '') === 'out')>Sortie</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Du</label>
        <input type="date" name="start" class="form-control" value="{{ $filters['start'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Au</label>
        <input type="date" name="end" class="form-control" value="{{ $filters['end'] ?? '' }}">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrer</button>
        <a href="{{ route('treasury.history.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
      </div>
    </form>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Type</th>
          <th>Catégorie</th>
          <th>Description</th>
          <th class="text-end">Montant</th>
          <th>Utilisateur</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $t)
          <tr>
            <td>{{ $t->date->format('d/m/Y') }}</td>
            <td class="text-muted">{{ $t->created_at->format('H:i') }}</td>
            <td>
              @if($t->type->value === 'in')
                <span class="badge bg-success">Entrée</span>
              @else
                <span class="badge bg-danger">Sortie</span>
              @endif
            </td>
            <td>{{ $t->categoryLabel() }}</td>
            <td>
              {{ $t->description ?? '—' }}
              @if($t->supplier_name || $t->product_reference)
                <div class="small text-muted">
                  @if($t->supplier_name) <i class="bi bi-truck me-1"></i>{{ $t->supplier_name }} @endif
                  @if($t->product_reference) <i class="bi bi-upc-scan ms-2 me-1"></i>{{ $t->product_reference }} @endif
                </div>
              @endif
            </td>
            <td class="text-end fw-semibold {{ $t->type->value === 'in' ? 'text-success' : 'text-danger' }}">
              {{ $t->type->value === 'in' ? '+' : '-' }}{{ number_format($t->amount, 0, ',', ' ') }} FCFA
            </td>
            <td>{{ $t->user->name ?? '—' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Aucune opération trouvée.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $transactions->links() }}</div>
</div>
@endsection
