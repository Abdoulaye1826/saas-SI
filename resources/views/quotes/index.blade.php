@extends('layouts.dashboard')

@section('title', 'Devis')
@section('page-title', 'Gestion des devis')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-file-earmark-ruled me-2"></i>Devis</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Devis</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('quotes.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouveau devis
  </a>
</div>

<div class="dashboard-summary-grid mb-4">
  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-primary">Total</span>
      <i class="bi bi-file-earmark-ruled summary-icon text-primary"></i>
    </div>
    <div class="summary-card-value">{{ $summary['total'] }}</div>
    <div class="summary-card-label">Devis enregistrés</div>
  </div>

  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-secondary">Brouillons</span>
      <i class="bi bi-pencil-square summary-icon text-secondary"></i>
    </div>
    <div class="summary-card-value">{{ $summary['draft'] }}</div>
    <div class="summary-card-label">Devis en brouillon</div>
  </div>

  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-info text-dark">Envoyés</span>
      <i class="bi bi-send summary-icon text-info"></i>
    </div>
    <div class="summary-card-value">{{ $summary['sent'] }}</div>
    <div class="summary-card-label">En attente de réponse</div>
  </div>

  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-success">Acceptés</span>
      <i class="bi bi-check-circle summary-icon text-success"></i>
    </div>
    <div class="summary-card-value">{{ $summary['accepted'] }}</div>
    <div class="summary-card-label">Prêts à convertir en vente</div>
  </div>

  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-danger">Refusés</span>
      <i class="bi bi-x-circle summary-icon text-danger"></i>
    </div>
    <div class="summary-card-value">{{ $summary['refused'] }}</div>
    <div class="summary-card-label">Devis refusés</div>
  </div>

  <div class="dashboard-summary-card">
    <div class="summary-card-top">
      <span class="badge bg-dark">Convertis</span>
      <i class="bi bi-arrow-right-circle summary-icon text-dark"></i>
    </div>
    <div class="summary-card-value">{{ $summary['converted'] }}</div>
    <div class="summary-card-label">Convertis en vente</div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-4 filter-card">
  <div class="card-body">
    <form method="GET" action="{{ route('quotes.index') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Rechercher</label>
        <input type="text" name="search" class="form-control" placeholder="Numéro, client"
               value="{{ $filters['search'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Statut</label>
        <select name="status" class="form-select">
          <option value="">Tous</option>
          <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Brouillon</option>
          <option value="sent" @selected(($filters['status'] ?? '') === 'sent')>Envoyé</option>
          <option value="accepted" @selected(($filters['status'] ?? '') === 'accepted')>Accepté</option>
          <option value="refused" @selected(($filters['status'] ?? '') === 'refused')>Refusé</option>
          <option value="converted" @selected(($filters['status'] ?? '') === 'converted')>Converti en vente</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Client</label>
        <input type="text" name="customer_id" class="form-control" placeholder="ID client"
               value="{{ $filters['customer_id'] ?? '' }}">
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary w-100">Réinitialiser</a>
      </div>
    </form>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Numéro</th>
          <th>Client</th>
          <th>Date</th>
          <th>Validité</th>
          <th class="text-end">Total</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($quotes as $quote)
          <tr>
            <td>{{ $quote->quote_number }}</td>
            <td>{{ $quote->customer?->full_name ?? 'Client anonyme' }}</td>
            <td>{{ $quote->quote_date->format('d/m/Y') }}</td>
            <td>
              @if($quote->valid_until)
                {{ $quote->valid_until->format('d/m/Y') }}
                @if($quote->isExpired())
                  <br><span class="badge bg-warning text-dark">Expiré</span>
                @endif
              @else
                —
              @endif
            </td>
            <td class="text-end">{{ number_format($quote->total_ttc, 0, ',', ' ') }} FCFA</td>
            <td><span class="badge {{ $quote->status->badgeClass() }}">{{ $quote->status->label() }}</span></td>
            <td class="text-end text-nowrap">
              <a href="{{ route('quotes.print', $quote) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Imprimer">
                <i class="bi bi-printer"></i>
              </a>
              <button type="button" class="btn btn-sm btn-outline-success js-whatsapp-share" title="Envoyer le PDF par WhatsApp"
                      data-payload-url="{{ route('quotes.whatsapp.payload', $quote) }}">
                <i class="bi bi-whatsapp"></i>
              </button>
              @if($quote->status->value === 'accepted')
                <form action="{{ route('quotes.convert', $quote) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Convertir ce devis en vente (brouillon) ?')">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-dark" title="Convertir en vente">
                    <i class="bi bi-arrow-right-circle"></i>
                  </button>
                </form>
              @endif
              @if($quote->status->value !== 'converted')
                <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('quotes.destroy', $quote) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce devis ?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Aucun devis trouvé.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $quotes->links() }}</div>
</div>

@push('scripts')
  @include('partials.whatsapp-share-script')
@endpush
@endsection
