@extends('layouts.dashboard')

@section('title', 'Boutique en ligne — Produits')
@section('page-title', 'Boutique en ligne')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-shop me-2"></i>Boutique en ligne</h1>
  <p class="text-muted small mb-0">Activez ou désactivez la présence en ligne, le statut vedette ou nouveauté de chaque produit, sans ouvrir sa fiche complète.</p>
</div>

@include('admin.store._nav')

<div class="mb-3">
  <span class="badge bg-primary fs-6">{{ $products->total() }} produit(s)</span>
</div>

<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('admin.store.products.index') }}" class="row g-3 align-items-end">
      <div class="col-md-5">
        <label class="form-label small">Rechercher</label>
        <input type="text" name="search" class="form-control" placeholder="Nom, référence, marque..."
               value="{{ $filters['search'] ?? '' }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">Catégorie</label>
        <select name="category_id" class="form-select">
          <option value="">Toutes</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small">Présence en ligne</label>
        <select name="show_on_store" class="form-select">
          <option value="">Toutes</option>
          <option value="1" @selected(($filters['show_on_store'] ?? '') === '1')>En ligne</option>
          <option value="0" @selected(($filters['show_on_store'] ?? '') === '0')>Hors ligne</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
      </div>
    </form>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead>
        <tr>
          <th>Produit</th>
          <th>Catégorie</th>
          <th class="text-end">Prix</th>
          <th class="text-center">Stock</th>
          <th class="text-center">Actif (comptoir)</th>
          <th class="text-center">Sur la boutique</th>
          <th class="text-center">Vedette</th>
          <th class="text-center">Nouveauté</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                @if($product->image)
                  <img src="{{ asset('storage/'.$product->image) }}" alt="" class="rounded" style="width:36px;height:36px;object-fit:cover;">
                @else
                  <span class="d-inline-flex align-items-center justify-content-center rounded bg-light text-muted" style="width:36px;height:36px;">
                    <i class="bi bi-controller"></i>
                  </span>
                @endif
                <div>
                  <div class="fw-medium">{{ $product->name }}</div>
                  <div class="text-muted small">{{ $product->reference }}</div>
                </div>
              </div>
            </td>
            <td>{{ $product->category?->name ?? '—' }}</td>
            <td class="text-end">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</td>
            <td class="text-center">
              <span class="badge {{ $product->stock_quantity > 0 ? 'bg-secondary' : 'bg-danger' }}">{{ $product->stock_quantity }}</span>
            </td>
            <td class="text-center">
              @if($product->is_active)
                <span class="badge bg-success">Actif</span>
              @else
                <span class="badge bg-secondary">Inactif</span>
              @endif
            </td>
            @foreach([
              'show_on_store' => 'Afficher sur la boutique|Retirer de la boutique',
              'is_featured' => 'Mettre en vedette|Retirer de la vedette',
              'is_new' => 'Marquer comme nouveauté|Retirer la nouveauté',
            ] as $field => $titles)
              @php([$onTitle, $offTitle] = explode('|', $titles))
              <td class="text-center">
                <form method="POST" action="{{ route('admin.store.products.toggle', [$product, $field]) }}" class="d-inline-block">
                  @csrf
                  <div class="form-check form-switch d-flex justify-content-center m-0">
                    <input class="form-check-input" type="checkbox" role="switch" style="cursor:pointer;"
                           {{ $product->{$field} ? 'checked' : '' }}
                           onchange="this.closest('form').submit()"
                           title="{{ $product->{$field} ? $offTitle : $onTitle }}">
                  </div>
                </form>
              </td>
            @endforeach
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Aucun produit trouvé.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($products->hasPages())
    <div class="p-3 border-top">{{ $products->links() }}</div>
  @endif
</div>
@endsection
