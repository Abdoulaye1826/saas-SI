@extends('layouts.dashboard')

@section('title', 'Avis clients')
@section('page-title', 'Avis clients')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-star me-2"></i>Avis clients</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Avis clients</li>
      </ol>
    </nav>
  </div>
</div>

<div class="mb-3">
  <span class="badge bg-primary fs-6">{{ $reviews->total() }} avis</span>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('product-reviews.index') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Statut</label>
        <select name="status" class="form-select">
          <option value="">Tous</option>
          @foreach(\App\Enums\ReviewStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
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
          <th>Produit</th>
          <th>Client</th>
          <th>Note</th>
          <th>Commentaire</th>
          <th>Date</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
          <tr>
            <td>{{ $review->product?->name ?? '—' }}</td>
            <td>{{ $review->customer?->full_name ?? '—' }}</td>
            <td>
              @for($i = 1; $i <= 5; $i++)
                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
              @endfor
            </td>
            <td style="max-width:280px;">{{ $review->comment ?: '—' }}</td>
            <td>{{ $review->created_at->format('d/m/Y') }}</td>
            <td><span class="badge {{ $review->status->badgeClass() }}">{{ $review->status->label() }}</span></td>
            <td class="text-end">
              @if($review->status->value !== 'approved')
                <form action="{{ route('product-reviews.approve', $review) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success" title="Valider"><i class="bi bi-check-lg"></i></button>
                </form>
              @endif
              @if($review->status->value !== 'rejected')
                <form action="{{ route('product-reviews.reject', $review) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Refuser"><i class="bi bi-x-lg"></i></button>
                </form>
              @endif
              @if($review->status->value !== 'hidden')
                <form action="{{ route('product-reviews.hide', $review) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-secondary" title="Masquer"><i class="bi bi-eye-slash"></i></button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Aucun avis pour le moment.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $reviews->links() }}</div>
</div>
@endsection
