@extends('layouts.storefront')

@section('title', ($settings->meta_title ?: $settings->name ?: $entreprise->name))
@section('canonical', route('store.home'))
@if($settings->hero_image_url)
    @section('og_image', $settings->hero_image_url)
@endif

@section('content')

{{-- ── Bannière principale ──────────────────────────────────────── --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, var(--store-primary) 0%, var(--store-secondary) 100%);">
    {{-- Halos décoratifs animés (CSS pur) : dérivés de la palette de marque,
         jamais de couleur codée en dur — s'adaptent automatiquement si le
         commerçant change ses couleurs dans Apparence. --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 -left-16 h-80 w-80 rounded-full bg-white/10 blur-3xl animate-drift"></div>
        <div class="absolute -bottom-32 -right-10 h-96 w-96 rounded-full bg-black/10 blur-3xl animate-drift" style="animation-delay:-7s;"></div>
    </div>
    @if($settings->hero_image_url)
        <img src="{{ $settings->hero_image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-20">
    @endif
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-white">
        <span class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-white/80 mb-4 animate-fade-up">
            <i class="bi bi-controller"></i> {{ $settings->name ?: $entreprise->name }}
        </span>
        <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl font-bold max-w-2xl leading-[1.08] tracking-tight animate-fade-up" style="animation-delay:.05s;">
            {{ $settings->hero_title ?: 'Les meilleures consoles et accessoires au meilleur prix' }}
        </h1>
        @if($settings->hero_subtitle)
            <p class="mt-5 text-lg text-white/85 max-w-xl animate-fade-up" style="animation-delay:.1s;">{{ $settings->hero_subtitle }}</p>
        @endif
        <div class="flex flex-wrap items-center gap-3 mt-9 animate-fade-up" style="animation-delay:.15s;">
            <a href="{{ $settings->hero_button_url ?: route('store.products.index') }}"
               class="group inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-slate-900 bg-white hover:bg-white/90 active:scale-95 transition-all shadow-lg shadow-black/10">
                {{ $settings->hero_button_label ?: 'Découvrir nos produits' }}
                <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
            </a>
            @if($settings->whatsapp_number)
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl font-semibold text-white border border-white/30 hover:bg-white/10 active:scale-95 transition-all">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>

{{-- ── Catégories ────────────────────────────────────────────────── --}}
@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14" data-reveal>
    <div class="flex items-center gap-2 mb-6">
        <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
        <h2 class="font-display text-xl font-bold text-slate-900">Catégories</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('store.categories.show', $category) }}" class="group text-center">
                <div class="aspect-square rounded-2xl bg-neutral-50 overflow-hidden flex items-center justify-center mb-2.5 ring-1 ring-transparent transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-lg group-hover:ring-[--store-primary]">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <i class="bi bi-tags text-3xl text-slate-300 transition-colors group-hover:text-[--store-primary]"></i>
                    @endif
                </div>
                <div class="text-sm font-medium text-slate-700 group-hover:text-slate-950 transition-colors">{{ $category->name }}</div>
                <div class="text-xs text-slate-400 store-figures">{{ $category->products_count }} produit(s)</div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ── Produits vedettes ────────────────────────────────────────── --}}
@if($featuredProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" data-reveal>
    <div class="flex items-end justify-between mb-6">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">Coup de cœur</div>
                <h2 class="font-display text-xl font-bold text-slate-900">Produits populaires</h2>
            </div>
        </div>
        <a href="{{ route('store.products.index') }}" class="store-link text-sm font-medium hover:underline whitespace-nowrap">Voir tout →</a>
    </div>
    @include('storefront.partials.product-carousel', ['products' => $featuredProducts])
</section>
@endif

{{-- ── Nouveautés ───────────────────────────────────────────────── --}}
@if($newProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" data-reveal>
    <div class="flex items-end justify-between mb-6">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">Fraîchement arrivé</div>
                <h2 class="font-display text-xl font-bold text-slate-900">Nouveautés</h2>
            </div>
        </div>
        <a href="{{ route('store.products.index', ['is_new' => 1]) }}" class="store-link text-sm font-medium hover:underline whitespace-nowrap">Voir tout →</a>
    </div>
    @include('storefront.partials.product-carousel', ['products' => $newProducts])
</section>
@endif

{{-- ── Promotions ───────────────────────────────────────────────── --}}
@if($promoProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-16" data-reveal>
    <div class="flex items-end justify-between mb-6">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">Offres limitées</div>
                <h2 class="font-display text-xl font-bold text-slate-900">Promotions</h2>
            </div>
        </div>
        <a href="{{ route('store.products.index', ['is_promo' => 1]) }}" class="store-link text-sm font-medium hover:underline whitespace-nowrap">Voir tout →</a>
    </div>
    @include('storefront.partials.product-carousel', ['products' => $promoProducts])
</section>
@endif

@if($featuredProducts->isEmpty() && $newProducts->isEmpty() && $promoProducts->isEmpty() && $categories->isEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center text-slate-400">
    <i class="bi bi-box-seam text-4xl mb-3 block"></i>
    Aucun produit n'est encore publié sur la boutique.
</section>
@endif

@endsection
