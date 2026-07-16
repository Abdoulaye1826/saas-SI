@extends('layouts.dashboard')

@section('title', $type === 'out' ? "Sorties d'argent" : "Entrées d'argent")
@section('page-title', $type === 'out' ? "Sorties d'argent" : "Entrées d'argent")

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-{{ $type === 'out' ? 'arrow-up-circle' : 'arrow-down-circle' }} me-2"></i>{{ $type === 'out' ? "Sorties d'argent" : "Entrées d'argent" }}</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">{{ $type === 'out' ? "Sorties d'argent" : "Entrées d'argent" }}</li>
      </ol>
    </nav>
  </div>
  @can('create', \App\Models\FinancialTransaction::class)
    <a href="{{ route('finance.transactions.create', ['type' => $type]) }}" class="btn btn-primary">
      <i class="bi bi-plus-lg me-1"></i>Nouvelle {{ $type === 'out' ? 'sortie' : 'entrée' }}
    </a>
  @endcan
</div>

<div class="card border-0 shadow-sm filter-card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('finance.transactions.index') }}" class="row g-3 align-items-end">
      <input type="hidden" name="type" value="{{ $type }}">
      <div class="col-md-3">
        <label class="form-label small">Rechercher</label>
        <input type="text" name="search" class="form-control" placeholder="Référence, description..." value="{{ $filters['search'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Catégorie</label>
        <select name="category" class="form-select">
          <option value="">Toutes</option>
          @foreach($categories as $category)
            <option value="{{ $category->value }}" @selected(($filters['category'] ?? '') === $category->value)>{{ $category->label() }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Compte</label>
        <select name="financial_account_id" class="form-select">
          <option value="">Tous</option>
          @foreach($accounts as $account)
            <option value="{{ $account->id }}" @selected(($filters['financial_account_id'] ?? '') == $account->id)>{{ $account->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
        <a href="{{ route('finance.transactions.index', ['type' => $type]) }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
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
          <th>Compte</th>
          <th>Catégorie</th>
          <th>Client/Fournisseur</th>
          <th>Référence</th>
          <th class="text-end">Montant</th>
          <th>Utilisateur</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions as $transaction)
          <tr>
            <td>{{ $transaction->date->format('d/m/Y') }}</td>
            <td>{{ $transaction->account->name }}</td>
            <td>
              {{ $transaction->category->label() }}
              @if($transaction->is_auto_generated)
                <span class="badge bg-secondary" title="Générée automatiquement">Auto</span>
              @endif
            </td>
            <td>{{ $transaction->customer?->full_name ?? $transaction->supplier?->name ?? '—' }}</td>
            <td>{{ $transaction->reference ?? '—' }}</td>
            <td class="text-end amount {{ $type === 'out' ? 'text-danger' : 'text-success' }}">
              {{ number_format((float) $transaction->amount, 0, ',', ' ') }} FCFA
            </td>
            <td>{{ $transaction->user->name }}</td>
            <td class="text-end">
              @if($transaction->attachment_path)
                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($transaction->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Justificatif">
                  <i class="bi bi-paperclip"></i>
                </a>
              @endif
              @can('update', $transaction)
                <a href="{{ route('finance.transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
              @endcan
              @can('delete', $transaction)
                <form action="{{ route('finance.transactions.destroy', $transaction) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette écriture ?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
              @endcan
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Aucune écriture trouvée.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $transactions->links() }}</div>
</div>
@endsection
