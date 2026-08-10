@extends('layouts.storefront')

@section('title', 'Boutique — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-2 mb-6">
        <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
        <h1 class="font-display text-2xl font-bold text-slate-950">Boutique</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- ── Filtres ─────────────────────────────────────────── --}}
        <aside class="lg:col-span-1">
            <form method="GET" action="{{ route('store.products.index') }}"
                  class="space-y-6 text-sm bg-white border border-slate-100 rounded-2xl p-5 lg:sticky lg:top-24"
                  style="accent-color: var(--store-primary);">
                <div>
                    <label for="search" class="block font-semibold text-slate-700 mb-1.5">Rechercher</label>
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Nom, référence, marque..."
                               class="w-full rounded-xl border-slate-200 pl-9 focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary] text-sm transition-colors">
                    </div>
                </div>

                <div>
                    <div class="font-semibold text-slate-700 mb-2">Catégorie</div>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                            <input type="radio" name="category_id" value="" {{ empty($filters['category_id'] ?? null) ? 'checked' : '' }}>
                            <span>Toutes</span>
                        </label>
                        @foreach($categories as $category)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                                <input type="radio" name="category_id" value="{{ $category->id }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category->id ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="font-semibold text-slate-700 mb-2">Prix (FCFA)</div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" class="w-full rounded-xl border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                        <span class="text-slate-300">—</span>
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max" class="w-full rounded-xl border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                    </div>
                </div>

                <div>
                    <div class="font-semibold text-slate-700 mb-2">Disponibilité</div>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                            <input type="radio" name="availability" value="" {{ empty($filters['availability'] ?? null) ? 'checked' : '' }}>
                            <span>Toutes</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                            <input type="radio" name="availability" value="in_stock" {{ ($filters['availability'] ?? '') === 'in_stock' ? 'checked' : '' }}>
                            <span>En stock</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                        <input type="checkbox" name="is_new" value="1" {{ !empty($filters['is_new'] ?? null) ? 'checked' : '' }}>
                        <span>Nouveautés</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer hover:text-slate-950 transition-colors">
                        <input type="checkbox" name="is_promo" value="1" {{ !empty($filters['is_promo'] ?? null) ? 'checked' : '' }}>
                        <span>Promotions</span>
                    </label>
                </div>

                <button type="submit" class="w-full rounded-xl py-2.5 font-semibold text-white transition-all active:scale-95 hover:brightness-110" style="background: var(--store-button);">
                    Filtrer
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('store.products.index') }}" class="block text-center text-slate-400 text-xs mt-2 hover:text-slate-600 transition-colors">Réinitialiser les filtres</a>
                @endif
            </form>
        </aside>

        {{-- ── Résultats ───────────────────────────────────────── --}}
        <div class="lg:col-span-3">
            <div class="text-sm text-slate-500 mb-4 store-figures">{{ $products->total() }} produit(s)</div>

            @if($products->isEmpty())
                <div class="text-center text-slate-400 py-24">
                    <i class="bi bi-search text-4xl mb-3 block"></i>
                    Aucun produit ne correspond à votre recherche.
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($products as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links('storefront.partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
