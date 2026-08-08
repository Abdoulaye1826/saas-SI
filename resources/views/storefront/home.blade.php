@extends('layouts.storefront')

@section('title', ($settings->meta_title ?: $settings->name ?: $entreprise->name))

@section('content')

{{-- ── Bannière principale ──────────────────────────────────────── --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, var(--store-primary) 0%, var(--store-secondary) 100%);">
    @if($settings->hero_image_url)
        <img src="{{ $settings->hero_image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25">
    @endif
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-white">
        <h1 class="text-3xl sm:text-5xl font-extrabold max-w-2xl leading-tight">
            {{ $settings->hero_title ?: 'Les meilleures consoles et accessoires au meilleur prix' }}
        </h1>
        @if($settings->hero_subtitle)
            <p class="mt-4 text-lg text-white/85 max-w-xl">{{ $settings->hero_subtitle }}</p>
        @endif
        <a href="{{ $settings->hero_button_url ?: route('store.products.index') }}"
           class="inline-flex items-center gap-2 mt-8 px-6 py-3 rounded-lg font-semibold text-slate-900 bg-white hover:bg-white/90 transition">
            {{ $settings->hero_button_label ?: 'Découvrir nos produits' }}
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>

{{-- ── Catégories ────────────────────────────────────────────────── --}}
@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-xl font-bold text-slate-900 mb-6">Catégories</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('store.categories.show', $category) }}" class="group text-center">
                <div class="aspect-square rounded-xl bg-slate-50 overflow-hidden flex items-center justify-center mb-2 group-hover:shadow-md transition">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
                    @else
                        <i class="bi bi-tags text-3xl text-slate-300"></i>
                    @endif
                </div>
                <div class="text-sm font-medium text-slate-700">{{ $category->name }}</div>
                <div class="text-xs text-slate-400">{{ $category->products_count }} produit(s)</div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ── Produits vedettes ────────────────────────────────────────── --}}
@if($featuredProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900">Produits populaires</h2>
        <a href="{{ route('store.products.index') }}" class="store-link text-sm font-medium">Voir tout →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($featuredProducts as $product)
            @include('storefront.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- ── Nouveautés ───────────────────────────────────────────────── --}}
@if($newProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900">Nouveautés</h2>
        <a href="{{ route('store.products.index', ['is_new' => 1]) }}" class="store-link text-sm font-medium">Voir tout →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($newProducts as $product)
            @include('storefront.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

{{-- ── Promotions ───────────────────────────────────────────────── --}}
@if($promoProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900">Promotions</h2>
        <a href="{{ route('store.products.index', ['is_promo' => 1]) }}" class="store-link text-sm font-medium">Voir tout →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($promoProducts as $product)
            @include('storefront.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@if($featuredProducts->isEmpty() && $newProducts->isEmpty() && $promoProducts->isEmpty() && $categories->isEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center text-slate-400">
    <i class="bi bi-box-seam text-4xl mb-3 block"></i>
    Aucun produit n'est encore publié sur la boutique.
</section>
@endif

@endsection
