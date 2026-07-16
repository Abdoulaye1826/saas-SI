@extends('layouts.dashboard')

@section('title', 'Comptes financiers')
@section('page-title', 'Comptes financiers')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-bank me-2"></i>Comptes financiers</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Comptes financiers</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('finance.accounts.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouveau compte
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Mode de paiement</th>
          <th class="text-end">Solde actuel</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($accounts as $account)
          <tr>
            <td><i class="bi {{ $account->type->icon() }} me-2"></i>{{ $account->name }}</td>
            <td>{{ $account->type->label() }}</td>
            <td>{{ $account->payment_method?->label() ?? '—' }}</td>
            <td class="text-end amount">{{ number_format((float) $account->current_balance, 0, ',', ' ') }} FCFA</td>
            <td>
              @if($account->is_active)
                <span class="badge bg-success">Actif</span>
              @else
                <span class="badge bg-secondary">Inactif</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('finance.accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('finance.accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce compte ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Aucun compte financier — <a href="{{ route('finance.accounts.create') }}">en créer un</a>.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
