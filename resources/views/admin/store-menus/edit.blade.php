@extends('layouts.dashboard')

@section('title', 'Modifier élément de menu')
@section('page-title', 'Modifier élément de menu')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-pencil me-2"></i>Modifier : {{ $menu->label }}</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('store-menus.index') }}">Menu</a></li>
      <li class="breadcrumb-item active">Modifier</li>
    </ol>
  </nav>
</div>

<div class="form-shell u-animate" style="max-width:560px;">
  <form method="POST" action="{{ route('store-menus.update', $menu) }}" data-ui-form novalidate>
    @csrf @method('PUT')
    <div class="form-card">
      <div class="form-card__header">
        <h2><i class="bi bi-list-ul"></i>Élément de menu</h2>
        <p class="form-card__subtitle">Les champs marqués <span class="req">*</span> sont obligatoires.</p>
      </div>
      <div class="form-card__body">
        @include('admin.store-menus._form')
      </div>
      <div class="form-card__footer">
        <a href="{{ route('store-menus.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Mettre à jour</button>
      </div>
    </div>
  </form>
</div>
@endsection
