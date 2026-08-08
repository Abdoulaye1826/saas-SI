@extends('layouts.dashboard')

@section('title', 'Nouvelle page')
@section('page-title', 'Nouvelle page')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-file-earmark-text me-2"></i>Nouvelle page</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('store-pages.index') }}">Pages</a></li>
      <li class="breadcrumb-item active">Nouvelle</li>
    </ol>
  </nav>
</div>

<div class="form-shell u-animate" style="max-width:720px;">
  <form method="POST" action="{{ route('store-pages.store') }}" data-ui-form novalidate>
    @csrf
    <div class="form-card">
      <div class="form-card__header">
        <h2><i class="bi bi-file-earmark-text"></i>Contenu de la page</h2>
        <p class="form-card__subtitle">Les champs marqués <span class="req">*</span> sont obligatoires.</p>
      </div>
      <div class="form-card__body">
        @include('admin.store-pages._form')
      </div>
      <div class="form-card__footer">
        <a href="{{ route('store-pages.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      </div>
    </div>
  </form>
</div>
@endsection
