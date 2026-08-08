@extends('layouts.dashboard')

@section('title', 'Commande '.$order->order_number)
@section('page-title', 'Commande '.$order->order_number)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-bag-check me-2"></i>Commande {{ $order->order_number }}</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('online-orders.index') }}">Commandes en ligne</a></li>
        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
      </ol>
    </nav>
  </div>
  <span class="badge {{ $order->status->badgeClass() }} fs-6">{{ $order->status->label() }}</span>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="table-card mb-3">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-box-seam me-2"></i>Articles</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Produit</th>
              <th class="text-center">Qté</th>
              <th class="text-end">Prix unitaire</th>
              <th class="text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
              <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end text-muted">Sous-total</td>
              <td class="text-end">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
              <td colspan="3" class="text-end text-muted">Livraison ({{ $order->delivery_method === 'pickup' ? 'Retrait' : ($order->delivery_zone === 'dakar' ? 'Dakar' : 'Hors Dakar') }})</td>
              <td class="text-end">{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ').' FCFA' : 'Gratuit' }}</td>
            </tr>
            <tr class="fw-bold">
              <td colspan="3" class="text-end">Total</td>
              <td class="text-end">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($order->notes)
      <div class="table-card mb-3 p-3">
        <h6 class="fw-semibold mb-2"><i class="bi bi-chat-text me-2"></i>Note du client</h6>
        <p class="mb-0 text-muted">{{ $order->notes }}</p>
      </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="table-card mb-3 p-3">
      <h6 class="fw-semibold mb-3"><i class="bi bi-person me-2"></i>Client</h6>
      <div class="small text-muted mb-1">Nom</div>
      <div class="mb-2">{{ $order->guest_name }}</div>
      <div class="small text-muted mb-1">Téléphone</div>
      <div class="mb-2">{{ $order->guest_phone }}</div>
      @if($order->guest_email)
        <div class="small text-muted mb-1">Email</div>
        <div class="mb-2">{{ $order->guest_email }}</div>
      @endif
      @if($order->customer)
        <a href="{{ route('customers.edit', $order->customer) }}" class="small">Voir la fiche client →</a>
      @endif
    </div>

    <div class="table-card mb-3 p-3">
      <h6 class="fw-semibold mb-3"><i class="bi bi-truck me-2"></i>Livraison</h6>
      <div class="small text-muted mb-1">Mode</div>
      <div class="mb-2">{{ $order->delivery_method === 'pickup' ? 'Retrait en boutique' : 'Livraison à domicile' }}</div>
      @if($order->delivery_method !== 'pickup')
        <div class="small text-muted mb-1">Adresse</div>
        <div class="mb-2">{{ $order->delivery_address }}, {{ $order->delivery_city }}</div>
      @endif
      @if($order->assignedDriver)
        <div class="small text-muted mb-1">Livreur affecté</div>
        <div class="mb-2">{{ $order->assignedDriver->name }}</div>
      @endif
    </div>

    <div class="table-card mb-3 p-3">
      <h6 class="fw-semibold mb-3"><i class="bi bi-credit-card me-2"></i>Paiement</h6>
      <div class="mb-2">Paiement à la livraison</div>
      @if($order->sale?->invoice)
        <a href="{{ route('invoices.edit', $order->sale->invoice) }}" class="small">Voir la facture {{ $order->sale->invoice->invoice_number }} →</a>
      @endif
    </div>

    <div class="table-card p-3">
      <h6 class="fw-semibold mb-3"><i class="bi bi-gear me-2"></i>Actions</h6>

      @if($order->isNew())
        <form method="POST" action="{{ route('online-orders.confirm', $order) }}" class="mb-2" onsubmit="return confirm('Confirmer cette commande ? Une vente et une facture seront générées, et le stock décrémenté.')">
          @csrf
          <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Confirmer la commande</button>
        </form>
      @endif

      @if(!$order->isNew() && !$order->isCancelled() && $order->status->value !== 'delivered')
        <form method="POST" action="{{ route('online-orders.update-status', $order) }}" class="mb-2">
          @csrf
          <div class="mb-2">
            <label class="form-label small">Prochain statut</label>
            <select name="status" class="form-select form-select-sm">
              @foreach($order->status->allowedNextStatuses() as $next)
                @if($next->value !== 'cancelled')
                  <option value="{{ $next->value }}">{{ $next->label() }}</option>
                @endif
              @endforeach
            </select>
          </div>
          @if($drivers->isNotEmpty())
            <div class="mb-2">
              <label class="form-label small">Livreur</label>
              <select name="assigned_driver_id" class="form-select form-select-sm">
                <option value="">— Aucun —</option>
                @foreach($drivers as $driver)
                  <option value="{{ $driver->id }}" @selected($order->assigned_driver_id === $driver->id)>{{ $driver->name }}</option>
                @endforeach
              </select>
            </div>
          @endif
          <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-arrow-right-circle me-1"></i>Mettre à jour le statut</button>
        </form>
      @endif

      @if(!$order->isCancelled() && $order->status->value !== 'delivered')
        <form method="POST" action="{{ route('online-orders.cancel', $order) }}" onsubmit="return confirm('Annuler cette commande ?{{ $order->isConfirmed() ? ' Le stock sera restitué et la facture liée annulée.' : '' }}')">
          @csrf
          <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-x-circle me-1"></i>Annuler la commande</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
