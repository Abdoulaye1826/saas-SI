@extends('layouts.dashboard')

@section('title', 'Avances clients')
@section('page-title', 'Avances clients')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-person-check me-2"></i>Avances clients</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Avances clients</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('finance.client-advances.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouvelle avance
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Client</th>
          <th>Mode de paiement</th>
          <th class="text-end">Montant</th>
          <th class="text-end">Utilisé</th>
          <th class="text-end">Restant</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($advances as $advance)
          <tr>
            <td>{{ $advance->date->format('d/m/Y') }}</td>
            <td>{{ $advance->customer->full_name }}</td>
            <td>{{ $advance->payment_method->label() }}</td>
            <td class="text-end amount">{{ number_format((float) $advance->amount, 0, ',', ' ') }} FCFA</td>
            <td class="text-end amount">{{ number_format((float) $advance->amount_used, 0, ',', ' ') }} FCFA</td>
            <td class="text-end amount">{{ number_format($advance->remaining_amount, 0, ',', ' ') }} FCFA</td>
            <td class="text-end">
              @if($advance->remaining_amount > 0.01)
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#applyModal{{ $advance->id }}">
                  <i class="bi bi-arrow-right-circle me-1"></i>Utiliser
                </button>

                <div class="modal fade" id="applyModal{{ $advance->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="{{ route('finance.client-advances.apply', $advance) }}">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title">Utiliser l'avance de {{ $advance->customer->full_name }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Facture à régler</label>
                            <select name="invoice_id" class="form-select" required>
                              @foreach(\App\Models\Invoice::where('customer_id', $advance->customer_id)->where('status', '!=', 'paid')->get() as $invoice)
                                <option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} — Reste {{ number_format($invoice->remaining_amount, 0, ',', ' ') }} FCFA</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Montant à appliquer</label>
                            <input type="number" step="0.01" name="amount" class="form-control" max="{{ $advance->remaining_amount }}" value="{{ $advance->remaining_amount }}" required>
                            <div class="form-text">Solde disponible : {{ number_format($advance->remaining_amount, 0, ',', ' ') }} FCFA</div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                          <button type="submit" class="btn btn-primary">Appliquer</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-4">Aucune avance client enregistrée.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $advances->links() }}</div>
</div>
@endsection
