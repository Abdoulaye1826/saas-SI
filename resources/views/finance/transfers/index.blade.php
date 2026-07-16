@extends('layouts.dashboard')

@section('title', 'Virements internes')
@section('page-title', 'Virements internes')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-arrow-left-right me-2"></i>Virements internes</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Virements internes</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('finance.transfers.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouveau virement
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>De</th>
          <th>Vers</th>
          <th class="text-end">Montant</th>
          <th>Motif</th>
          <th>Utilisateur</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transfers as $transfer)
          <tr>
            <td>{{ $transfer->date->format('d/m/Y') }}</td>
            <td>{{ $transfer->fromAccount->name }}</td>
            <td>{{ $transfer->toAccount->name }}</td>
            <td class="text-end amount">{{ number_format((float) $transfer->amount, 0, ',', ' ') }} FCFA</td>
            <td>{{ $transfer->reason ?? '—' }}</td>
            <td>{{ $transfer->user->name }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Aucun virement enregistré.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $transfers->links() }}</div>
</div>
@endsection
