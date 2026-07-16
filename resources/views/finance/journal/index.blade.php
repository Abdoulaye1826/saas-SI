@extends('layouts.dashboard')

@section('title', 'Journal de caisse')
@section('page-title', 'Journal de caisse')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-journal-text me-2"></i>Journal de caisse</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Journal de caisse</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('finance.journal.pdf', request()->query()) }}" class="btn btn-outline-secondary">
    <i class="bi bi-file-earmark-pdf me-1"></i>Imprimer (PDF)
  </a>
</div>

<div class="card border-0 shadow-sm filter-card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('finance.journal.index') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Compte</label>
        <select name="financial_account_id" class="form-select">
          @foreach($accounts as $acc)
            <option value="{{ $acc->id }}" @selected($acc->id === $account->id)>{{ $acc->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small">Date</label>
        <input type="date" name="date" class="form-control" value="{{ $date->toDateString() }}">
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Afficher</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-6 col-md-3">
    <div class="kpi-card"><div class="kpi-label">Solde ouverture</div><div class="kpi-value">{{ number_format($data['opening'], 0, ',', ' ') }} FCFA</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card"><div class="kpi-label">Entrées</div><div class="kpi-value text-success">{{ number_format($data['entries'], 0, ',', ' ') }} FCFA</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card"><div class="kpi-label">Sorties</div><div class="kpi-value text-danger">{{ number_format($data['exits'], 0, ',', ' ') }} FCFA</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card"><div class="kpi-label">Solde clôture</div><div class="kpi-value">{{ number_format($data['closing'], 0, ',', ' ') }} FCFA</div></div>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Heure</th>
          <th>Type</th>
          <th>Catégorie</th>
          <th>Client/Fournisseur</th>
          <th class="text-end">Montant</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data['transactions'] as $t)
          <tr>
            <td>{{ $t->created_at->format('H:i') }}</td>
            <td><span class="badge {{ $t->type->badgeClass() }}">{{ $t->type->label() }}</span></td>
            <td>{{ $t->category->label() }}</td>
            <td>{{ $t->customer?->full_name ?? $t->supplier?->name ?? '—' }}</td>
            <td class="text-end amount">{{ number_format((float) $t->amount, 0, ',', ' ') }} FCFA</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">Aucun mouvement ce jour-là.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
