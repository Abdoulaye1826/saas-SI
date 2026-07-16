@extends('layouts.dashboard')

@section('title', 'Avances fournisseurs')
@section('page-title', 'Avances fournisseurs')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-truck me-2"></i>Avances fournisseurs</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Avances fournisseurs</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('finance.supplier-advances.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouvelle avance
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Fournisseur</th>
          <th>Référence</th>
          <th>Observation</th>
          <th class="text-end">Montant</th>
        </tr>
      </thead>
      <tbody>
        @forelse($advances as $advance)
          <tr>
            <td>{{ $advance->date->format('d/m/Y') }}</td>
            <td>{{ $advance->supplier->name }}</td>
            <td>{{ $advance->reference ?? '—' }}</td>
            <td>{{ $advance->observation ?? '—' }}</td>
            <td class="text-end amount">{{ number_format((float) $advance->amount, 0, ',', ' ') }} FCFA</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">Aucune avance fournisseur enregistrée.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $advances->links() }}</div>
</div>
@endsection
