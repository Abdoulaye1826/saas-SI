@extends('layouts.dashboard')

@section('title', 'Menu de la boutique')
@section('page-title', 'Menu de la boutique')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-list-ul me-2"></i>Menu de navigation</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Menu</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('store-menus.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouvel élément
  </a>
</div>

<p class="text-muted small mb-3">
  Ces éléments s'affichent dans la navigation de la boutique, à la suite d'Accueil / Boutique et des catégories déjà publiées.
</p>

<form method="POST" action="{{ route('store-menus.reorder') }}">
  @csrf
  <div class="table-card">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th style="width:100px;">Ordre</th>
            <th>Libellé</th>
            <th>Lien</th>
            <th>Statut</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($menus as $menu)
            <tr>
              <td>
                <input type="number" name="order[{{ $menu->id }}]" value="{{ $menu->sort_order }}" min="0"
                       class="form-control form-control-sm" style="width:80px;">
              </td>
              <td>{{ $menu->label }}</td>
              <td><code class="small">{{ $menu->url }}</code>{{ $menu->opens_new_tab ? ' (nouvel onglet)' : '' }}</td>
              <td>
                <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $menu->is_active ? 'Actif' : 'Inactif' }}
                </span>
              </td>
              <td class="text-end">
                <a href="{{ route('store-menus.edit', $menu) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Aucun élément de menu personnalisé pour le moment.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($menus->isNotEmpty())
      <div class="p-3 border-top text-end">
        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-down-up me-1"></i>Enregistrer l'ordre</button>
      </div>
    @endif
  </div>
</form>

@if($menus->isNotEmpty())
  <div class="table-card mt-3">
    <div class="p-3 border-top-0">
      <h6 class="fw-semibold mb-3 small text-uppercase text-muted">Supprimer un élément</h6>
      <div class="d-flex flex-wrap gap-2">
        @foreach($menus as $menu)
          <form action="{{ route('store-menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Supprimer « {{ $menu->label }} » du menu ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash me-1"></i>{{ $menu->label }}
            </button>
          </form>
        @endforeach
      </div>
    </div>
  </div>
@endif
@endsection
