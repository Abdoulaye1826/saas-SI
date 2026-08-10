@extends('layouts.storefront')

@section('title', ($settings->meta_title ?: $settings->name ?: $entreprise->name))
@section('canonical', route('store.home'))
@if($settings->hero_image_url)
    @section('og_image', $settings->hero_image_url)
@endif

@section('content')

{{-- ── Hero immersif ─────────────────────────────────────────────────
     Grille technique + photo d'ambiance + halos de marque + particules
     flottantes + la manette (PNG détouré, vraie transparence alpha —
     plus de bidouille mix-blend-mode) fournie par le commerçant.
     La photo d'arrière-plan par défaut (images/photo/background.jpg,
     recompressée 1.5 Mo → 143 Ko côté serveur) cède la place dès que le
     commerçant renseigne sa propre bannière (Admin > Boutique en ligne >
     Apparence) — une seule source de vérité, jamais les deux en même temps.
     Parallax très léger au scroll (voir data-parallax, resources/js/app.js),
     désactivé sous 640px et si prefers-reduced-motion. --}}
@php
    $heroBgImage = $settings->hero_image_url ?: asset('images/photo/background.jpg');
@endphp
<section class="relative overflow-hidden gaming-arena gaming-scanline">
    {{-- Photo d'ambiance en fond, assombrie par un dégradé pour garantir
         la lisibilité du texte par-dessus (priorité du brief : lisibilité
         avant esthétique). --}}
    <img src="{{ $heroBgImage }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30" loading="eager" fetchpriority="high" aria-hidden="true">
    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(10,10,18,.55) 0%, rgba(10,10,18,.85) 62%, #0a0a12 100%);" aria-hidden="true"></div>

    {{-- Halos de marque (dérivés de --store-primary/--store-secondary) --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true" data-parallax="0.08">
        <div class="absolute -top-24 -left-16 h-80 w-80 rounded-full blur-3xl animate-drift" style="background: color-mix(in srgb, var(--store-primary) 45%, transparent);"></div>
        <div class="absolute -bottom-32 -right-10 h-96 w-96 rounded-full blur-3xl animate-drift" style="background: color-mix(in srgb, var(--store-secondary) 40%, transparent); animation-delay:-7s;"></div>
    </div>

    {{-- Particules flottantes (masquées sur mobile / reduced-motion via CSS) --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none hidden sm:block" aria-hidden="true">
        @for($i = 0; $i < 14; $i++)
            @php
                $size = rand(2, 4);
                $left = rand(2, 96);
                $duration = rand(9, 18);
                $delay = rand(0, 10);
            @endphp
            <span class="gaming-particle" style="
                width:{{ $size }}px; height:{{ $size }}px;
                left:{{ $left }}%; bottom:0;
                --particle-opacity:{{ rand(30,70) / 100 }};
                --particle-drift:{{ rand(-30,30) }}px;
                animation-duration:{{ $duration }}s;
                animation-delay:-{{ $delay }}s;
            "></span>
        @endfor
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-center">
            <div class="lg:col-span-3 text-white">
                <span class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.2em] mb-5 animate-fade-up px-3 py-1.5 rounded-full border border-white/15" style="background: color-mix(in srgb, var(--store-primary) 18%, transparent);">
                    <span class="h-1.5 w-1.5 rounded-full animate-glow-pulse" style="background: var(--store-primary);"></span>
                    {{ $settings->name ?: $entreprise->name }}
                </span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold max-w-2xl leading-[1.05] tracking-tight animate-fade-up" style="animation-delay:.05s;">
                    {{ $settings->hero_title ?: 'Les meilleures consoles et accessoires au meilleur prix' }}
                </h1>
                @if($settings->hero_subtitle)
                    <p class="mt-5 text-lg text-white/70 max-w-xl animate-fade-up" style="animation-delay:.1s;">{{ $settings->hero_subtitle }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-3 mt-9 animate-fade-up" style="animation-delay:.15s;">
                    <a href="{{ $settings->hero_button_url ?: route('store.products.index') }}"
                       class="group inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-slate-950 transition-all active:scale-95 shadow-lg"
                       style="background: white; box-shadow: 0 0 40px -8px color-mix(in srgb, var(--store-primary) 60%, transparent);">
                        {{ $settings->hero_button_label ?: 'Découvrir nos produits' }}
                        <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </a>
                    @if($settings->whatsapp_number)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl font-semibold text-white border border-white/20 hover:bg-white/10 active:scale-95 transition-all">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    @endif
                </div>
            </div>

            {{-- Manette produit (PNG détouré, vraie transparence alpha
                 vérifiée côté serveur — aucun bidouillage mix-blend-mode
                 nécessaire). Flottement lent + glow dérivé de la couleur
                 de marque, jamais de rotation rapide (brief §3). --}}
            <div class="lg:col-span-2 hidden lg:flex justify-center items-center" data-parallax="0.16" aria-hidden="true">
                <div class="relative animate-float-slow" style="filter: drop-shadow(0 30px 55px color-mix(in srgb, var(--store-primary) 50%, transparent));">
                    <img src="{{ asset('images/photo/menette.png') }}" alt="" class="w-full max-w-md">
                    <div class="absolute inset-0 rounded-full blur-3xl -z-10 animate-glow-pulse" style="background: color-mix(in srgb, var(--store-primary) 30%, transparent);"></div>
                </div>
            </div>
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
                <div class="relative aspect-square rounded-2xl bg-neutral-900 overflow-hidden flex items-center justify-center mb-2.5 transition-all duration-300 group-hover:-translate-y-1.5 group-hover:gaming-ring">
                    @if($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover opacity-80 transition-all duration-500 group-hover:scale-110 group-hover:opacity-100">
                    @else
                        <div class="absolute inset-0 gaming-arena opacity-60"></div>
                        <i class="bi bi-controller text-3xl text-white/40 relative transition-colors group-hover:text-white"></i>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/0 to-black/0"></div>
                    <span class="hud-corner hud-corner--tl"></span>
                    <span class="hud-corner hud-corner--tr"></span>
                    <span class="hud-corner hud-corner--bl"></span>
                    <span class="hud-corner hud-corner--br"></span>
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
<section class="relative overflow-hidden gaming-arena gaming-scanline mt-8" data-reveal>
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-1/2 left-1/4 -translate-y-1/2 h-64 w-64 rounded-full blur-3xl animate-glow-pulse" style="background: color-mix(in srgb, var(--store-primary) 35%, transparent);"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-end justify-between mb-6">
            <div class="flex items-center gap-2">
                <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
                <div>
                    <div class="text-[11px] font-semibold uppercase tracking-widest" style="color: color-mix(in srgb, var(--store-primary) 70%, white);">🔥 Offres du moment</div>
                    <h2 class="font-display text-xl font-bold text-white">Promotions</h2>
                </div>
            </div>
            <a href="{{ route('store.products.index', ['is_promo' => 1]) }}" class="text-sm font-medium hover:underline whitespace-nowrap text-white/80 hover:text-white">Voir tout →</a>
        </div>
        @include('storefront.partials.product-carousel', ['products' => $promoProducts, 'dark' => true])
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
