@extends('layouts.dashboard')

@section('title', 'Commandes en ligne')
@section('page-title', 'Commandes en ligne')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-bag-check me-2"></i>Commandes en ligne</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Commandes en ligne</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('store.home') }}" target="_blank" class="btn btn-outline-secondary">
    <i class="bi bi-box-arrow-up-right me-1"></i>Voir la boutique
  </a>
</div>

<div class="mb-3">
  <span class="badge bg-primary fs-6">{{ $orders->total() }} commande(s)</span>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('online-orders.index') }}" class="row g-3 align-items-end">
      <div class="col-md-5">
        <label class="form-label small">Rechercher</label>
        <input type="text" name="search" class="form-control" placeholder="Numéro, nom ou téléphone"
               value="{{ $filters['search'] ?? '' }}">
      </div>
      <div class="col-md-4">
        <label class="form-label small">Statut</label>
        <select name="status" class="form-select">
          <option value="">Tous</option>
          @foreach(\App\Enums\OnlineOrderStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 text-end">
        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
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
          <th>Articles</th>
          <th>Total</th>
          <th>Livraison</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>{{ $order->order_number }}</td>
            <td>
              {{ $order->guest_name }}
              <div class="text-muted small">{{ $order->guest_phone }}</div>
            </td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $order->items()->count() }}</td>
            <td>{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
            <td>{{ $order->delivery_method === 'pickup' ? 'Retrait' : 'Domicile' }}</td>
            <td><span class="badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span></td>
            <td class="text-end">
              <a href="{{ route('online-orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Aucune commande en ligne pour le moment.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $orders->links() }}</div>
</div>
@endsection
