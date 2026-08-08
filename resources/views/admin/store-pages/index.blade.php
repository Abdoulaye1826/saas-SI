@extends('layouts.dashboard')

@section('title', 'Pages de la boutique')
@section('page-title', 'Pages de la boutique')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-file-earmark-text me-2"></i>Pages</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Pages</li>
      </ol>
    </nav>
  </div>
  <a href="{{ route('store-pages.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nouvelle page
  </a>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Titre</th>
          <th>Slug</th>
          <th>Pied de page</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pages as $page)
          <tr>
            <td>{{ $page->title }}</td>
            <td><code>{{ $page->slug }}</code></td>
            <td>{{ $page->show_in_footer ? 'Oui' : 'Non' }}</td>
            <td>
              <span class="badge {{ $page->is_published ? 'bg-success' : 'bg-secondary' }}">
                {{ $page->is_published ? 'Publiée' : 'Brouillon' }}
              </span>
            </td>
            <td class="text-end">
              @if($page->is_published)
                <a href="{{ route('store.pages.show', $page) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir">
                  <i class="bi bi-box-arrow-up-right"></i>
                </a>
              @endif
              <a href="{{ route('store-pages.edit', $page) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('store-pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette page ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Aucune page créée pour le moment.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $pages->links() }}</div>
</div>
@endsection
