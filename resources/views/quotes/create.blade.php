@extends('layouts.dashboard')

@section('title', 'Nouveau devis')
@section('page-title', 'Nouveau devis')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-plus-circle me-2"></i>Nouveau devis</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Devis</a></li>
      <li class="breadcrumb-item active">Nouveau</li>
    </ol>
  </nav>
</div>

<div class="form-shell form-shell--wide u-animate">
  <form method="POST" action="{{ route('quotes.store') }}" data-ui-form novalidate>
    @csrf
    <div class="form-card">
      <div class="form-card__header">
        <h2><i class="bi bi-file-earmark-ruled"></i>Détails du devis</h2>
        <p class="form-card__subtitle">Renseignez le client et les produits proposés. Les champs marqués <span class="req">*</span> sont obligatoires.</p>
      </div>
      <div class="form-card__body">
        @include('quotes._form')
      </div>
      <div class="form-card__footer">
        <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      </div>
    </div>
  </form>
</div>

@include('sales._new_customer_modal')
@endsection
