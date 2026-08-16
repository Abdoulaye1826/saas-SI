@extends('layouts.dashboard')

@section('title', 'Cadeaux / Produits offerts')
@section('page-title', 'Cadeaux / Produits offerts')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-gift me-2"></i>Cadeaux / Produits offerts</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventes</a></li>
        <li class="breadcrumb-item active">Cadeaux</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('sales.create') }}" class="btn btn-primary">
    <i class="bi bi-gift-fill me-1"></i>Offrir un produit
  </a>
</div>

{{-- Filtres explicitement demandés au cahier §8 : client (via recherche),
     produit, date, utilisateur. --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('gifts.index') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Rechercher</label>
        <input type="text" name="search" class="form-control" placeholder="Numéro, client ou produit"
               value="{{ $filters['search'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Produit</label>
        <select name="product_id" class="form-select">
          <option value="">Tous</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>{{ $product->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Utilisateur</label>
        <select name="user_id" class="form-select">
          <option value="">Tous</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>{{ $user->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
      </div>
      <div class="col-md-3">
        <label class="form-label small">Du</label>
        <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Au</label>
        <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
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
          <th>Date</th>
          <th>Client</th>
          <th>Produit(s)</th>
          <th class="text-center">Qté</th>
          <th class="text-end">Valeur indicative</th>
          <th>Offert par</th>
          <th>Statut</th>
          <th class="text-end">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($gifts as $gift)
          <tr>
            <td>{{ $gift->gift_number }}</td>
            <td>{{ $gift->gift_date->format('d/m/Y H:i') }}</td>
            <td>{{ $gift->customer?->full_name ?? '—' }}</td>
            <td>
              @foreach($gift->items as $item)
                {{ $item->product?->name ?? '—' }}@if($item->productImei) <small class="text-muted">(IMEI {{ $item->productImei->imei }})</small>@endif
                @if(!$loop->last)<br>@endif
              @endforeach
              @if($gift->notes)
                <div class="small text-muted mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $gift->notes }}</div>
              @endif
            </td>
            <td class="text-center">{{ $gift->items->sum('quantity') }}</td>
            <td class="text-end amount">{{ number_format($gift->totalValue(), 0, ',', ' ') }} FCFA</td>
            <td>{{ $gift->user?->name ?? '—' }}</td>
            <td><span class="badge {{ $gift->status->badgeClass() }}">{{ $gift->status->label() }}</span></td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <a href="{{ route('gifts.print', $gift) }}" class="btn btn-sm btn-outline-secondary" title="Imprimer le bon de cadeau">
                  <i class="bi bi-printer"></i>
                </a>
                @if($gift->isGiven())
                  <form action="{{ route('gifts.cancel', $gift) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Annuler ce cadeau ? Le produit sera remis en stock. L\'opération reste visible dans l\'historique.')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Annuler / corriger">
                      <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-4">Aucun cadeau enregistré.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $gifts->links() }}</div>
</div>
@endsection
