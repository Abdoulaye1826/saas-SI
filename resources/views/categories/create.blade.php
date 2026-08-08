@extends('layouts.dashboard')

@section('title', 'Nouvelle catégorie')
@section('page-title', 'Nouvelle catégorie')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-plus-circle me-2"></i>Nouvelle catégorie</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
      <li class="breadcrumb-item active">Nouvelle</li>
    </ol>
  </nav>
</div>

<div class="form-shell u-animate" style="max-width:560px;">
  <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" data-ui-form novalidate>
    @csrf
    <div class="form-card">
      <div class="form-card__header">
        <h2><i class="bi bi-bookmarks"></i>Fiche catégorie</h2>
        <p class="form-card__subtitle">Les champs marqués <span class="req">*</span> sont obligatoires.</p>
      </div>
      <div class="form-card__body">
        @include('categories._form')
      </div>
      <div class="form-card__footer">
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      </div>
    </div>
  </form>
</div>
@endsection
