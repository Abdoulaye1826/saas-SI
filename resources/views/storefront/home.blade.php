@extends('layouts.storefront')

@section('title', ($settings->meta_title ?: $settings->name ?: $entreprise->name))
@section('canonical', route('store.home'))
@if($settings->hero_image_url)
    @section('og_image', $settings->hero_image_url)
@endif

@section('content')

{{-- ── Hero immersif ─────────────────────────────────────────────────
     Grille technique + halos de marque + particules flottantes + la
     photo produit (manette) fournie par le commerçant, détourée à la
     volée par fusion (mix-blend-mode: multiply — fond blanc de la photo
     studio qui se fond dans le fond sombre, sans retouche d'image).
     Parallax très léger au scroll (voir data-parallax, resources/js/app.js),
     désactivé sous 640px et si prefers-reduced-motion. --}}
<section class="relative overflow-hidden gaming-arena gaming-scanline">
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

    @if($settings->hero_image_url)
        <img src="{{ $settings->hero_image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-15">
    @endif

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

            {{-- Photo manette (fond studio blanc fusionné dans l'arène sombre) --}}
            <div class="lg:col-span-2 hidden lg:flex justify-center items-center" data-parallax="0.16" aria-hidden="true">
                <div class="relative animate-float-slow">
                    <div class="absolute inset-0 rounded-full blur-3xl animate-glow-pulse" style="background: color-mix(in srgb, var(--store-primary) 35%, transparent);"></div>
                    <img src="{{ asset('images/photo/'.rawurlencode('manette.jpeg')) }}" alt=""
                         class="relative w-full max-w-md mix-blend-multiply"
                         style="filter: drop-shadow(0 35px 45px color-mix(in srgb, var(--store-primary) 40%, transparent));">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Consoles en vitrine ──────────────────────────────────────────
     Brief §3 : "consoles animées" — photos produit du commerçant,
     flottement lent (jamais de rotation rapide), légère inclinaison 3D
     au survol, glow et ombre dynamique dérivés de la couleur de marque. --}}
@php
    $consoleShowcase = [
        ['file' => 'playstation 5.jpeg', 'label' => 'PlayStation 5', 'search' => 'PlayStation 5'],
        ['file' => 'playsatation 4.jpeg', 'label' => 'PlayStation 4', 'search' => 'PlayStation 4'],
        ['file' => 'xbox serie X.jpeg', 'label' => 'Xbox Series', 'search' => 'Xbox'],
    ];
@endphp
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14" data-reveal>
    <div class="flex items-center gap-2 mb-6">
        <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
        <h2 class="font-display text-xl font-bold text-slate-900">Consoles</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach($consoleShowcase as $i => $console)
            <a href="{{ route('store.products.index', ['search' => $console['search']]) }}"
               class="group block" style="perspective: 900px;">
                <div class="relative rounded-3xl gaming-arena overflow-hidden p-8 pb-6 text-center transition-transform duration-500 ease-out"
                     style="transform-style: preserve-3d;"
                     onmouseover="if(!window.matchMedia('(prefers-reduced-motion: reduce)').matches){this.style.transform='rotateY(6deg) rotateX(-4deg) scale(1.02)'}"
                     onmouseout="this.style.transform=''">
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none" aria-hidden="true">
                        <div class="absolute inset-x-0 top-0 h-1/2 blur-2xl" style="background: color-mix(in srgb, var(--store-primary) 30%, transparent);"></div>
                    </div>
                    <img src="{{ asset('images/photo/'.rawurlencode($console['file'])) }}" alt="{{ $console['label'] }}" loading="lazy"
                         class="relative mx-auto h-40 sm:h-48 w-auto object-contain mix-blend-multiply animate-float-slow"
                         style="animation-delay:-{{ $i * 1.8 }}s; filter: drop-shadow(0 20px 20px color-mix(in srgb, var(--store-primary) 35%, transparent));">
                    <div class="relative mx-auto mt-2 h-2.5 w-24 rounded-full blur-md bg-black/40"></div>
                    <div class="relative mt-4 font-display font-semibold text-white text-sm tracking-wide">{{ $console['label'] }}</div>
                    <span class="hud-corner hud-corner--tl"></span>
                    <span class="hud-corner hud-corner--tr"></span>
                    <span class="hud-corner hud-corner--bl"></span>
                    <span class="hud-corner hud-corner--br"></span>
                </div>
            </a>
        @endforeach
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
