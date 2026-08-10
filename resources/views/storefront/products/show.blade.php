@extends('layouts.storefront')

@section('title', ($product->meta_title ?: $product->name).' — '.($settings->name ?: $entreprise->name))
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@section('canonical', route('store.products.show', $product))
@section('og_type', 'product')
@section('og_title', $product->meta_title ?: $product->name)
@section('og_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155))
@if($product->image)
    @section('og_image', asset('storage/'.$product->image))
@endif

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-slate-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('store.home') }}" class="hover:text-slate-700 transition-colors">Accueil</a>
        <i class="bi bi-chevron-right text-[9px]"></i>
        <a href="{{ route('store.products.index') }}" class="hover:text-slate-700 transition-colors">Boutique</a>
        @if($product->category)
            <i class="bi bi-chevron-right text-[9px]"></i>
            <a href="{{ route('store.categories.show', $product->category) }}" class="hover:text-slate-700 transition-colors">{{ $product->category->name }}</a>
        @endif
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14">
        <div class="aspect-square rounded-3xl bg-neutral-50 overflow-hidden group relative">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}"
                     class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
            @else
                <div class="h-full w-full flex items-center justify-center text-slate-300">
                    <i class="bi bi-controller text-6xl"></i>
                </div>
            @endif
            @if($product->is_promo && $product->promo_price && (float) $product->sale_price > 0)
                @php($discountPct = round((1 - ((float) $product->promo_price / (float) $product->sale_price)) * 100))
                <span class="absolute top-4 left-4 text-xs font-bold uppercase tracking-wide px-3 py-1.5 rounded-full text-white shadow-lg" style="background: var(--store-primary);">
                    -{{ $discountPct }}% de réduction
                </span>
            @endif
        </div>

        <div class="animate-fade-up">
            @if($product->category)
                <div class="text-xs font-semibold uppercase tracking-widest mb-2" style="color: var(--store-primary);">{{ $product->category->name }}</div>
            @endif
            <h1 class="font-display text-2xl sm:text-3xl font-bold text-slate-950 mb-2 tracking-tight">{{ $product->name }}</h1>

            @if($product->average_rating !== null)
                <div class="flex items-center gap-2 mb-5">
                    <div class="flex text-amber-400 text-sm">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <span class="text-sm text-slate-500">{{ $product->average_rating }}/5 <span class="text-slate-300">·</span> {{ $product->approvedReviews->count() }} avis</span>
                </div>
            @endif

            <div class="flex items-baseline gap-3 mb-5 font-display store-figures">
                @if($product->is_promo && $product->promo_price)
                    <span class="text-4xl font-bold" style="color: var(--store-primary);">{{ number_format((float) $product->promo_price, 0, ',', ' ') }}<span class="text-base font-semibold ms-1">FCFA</span></span>
                    <span class="text-lg text-slate-400 line-through">{{ number_format((float) $product->sale_price, 0, ',', ' ') }}</span>
                @else
                    <span class="text-4xl font-bold text-slate-950">{{ number_format((float) $product->sale_price, 0, ',', ' ') }}<span class="text-base font-semibold text-slate-500 ms-1">FCFA</span></span>
                @endif
            </div>

            @if($product->show_stock)
                <div class="mb-6 inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-full {{ $product->stock_quantity > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $product->stock_quantity > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $product->stock_quantity > 0 ? $product->stock_quantity.' disponible(s)' : 'Rupture de stock' }}
                </div>
            @endif

            @if($product->description)
                <div class="text-sm leading-relaxed text-slate-600 max-w-none mb-8 whitespace-pre-line">{{ $product->description }}</div>
            @endif

            <div class="space-y-3">
                @if($product->allow_order && $product->stock_quantity > 0)
                    <form method="POST" action="{{ route('store.cart.add') }}" class="flex items-center gap-3"
                          @unless($product->tracks_imei) x-data="{ qty: 1, max: {{ $product->stock_quantity }} }" @endunless>
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @unless($product->tracks_imei)
                            <div class="inline-flex items-center rounded-xl border border-slate-200 overflow-hidden">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-10 h-12 flex items-center justify-center text-slate-500 hover:bg-neutral-50 active:scale-90 transition" aria-label="Diminuer la quantité">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <input type="number" name="quantity" x-model.number="qty" min="1" :max="max"
                                       class="w-12 h-12 border-0 text-center text-sm font-semibold store-figures focus:ring-0 p-0">
                                <button type="button" @click="qty = Math.min(max, qty + 1)" class="w-10 h-12 flex items-center justify-center text-slate-500 hover:bg-neutral-50 active:scale-90 transition" aria-label="Augmenter la quantité">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        @endunless
                        <button type="submit"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-white transition-all active:scale-95 hover:brightness-110 shadow-lg store-glow"
                                style="background: var(--store-button);">
                            <i class="bi bi-cart-plus"></i> Ajouter au panier
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-white opacity-40 cursor-not-allowed"
                            style="background: var(--store-button);">
                        <i class="bi bi-cart-x"></i> {{ $product->stock_quantity > 0 ? 'Commande indisponible' : 'Rupture de stock' }}
                    </button>
                @endif

                @if($settings->whatsapp_number && $product->allow_order)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ rawurlencode('Bonjour, je suis intéressé(e) par : '.$product->name) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-white bg-green-600 hover:bg-green-700 active:scale-95 transition-all">
                        <i class="bi bi-whatsapp"></i> Commander sur WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Avis clients ─────────────────────────────────────────── --}}
    <div class="mt-20 max-w-3xl border-t border-slate-100 pt-12" data-reveal>
        <h2 class="font-display text-xl font-bold text-slate-900 mb-6">Avis clients</h2>

        @if(session('success'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 text-green-800 text-sm px-4 py-3 border border-green-100"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 text-red-700 text-sm px-4 py-3 border border-red-100"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
        @endif

        @if($product->approvedReviews->isEmpty())
            <p class="text-sm text-slate-400 mb-6">Aucun avis pour ce produit pour le moment.</p>
        @else
            <div class="space-y-3 mb-8">
                @foreach($product->approvedReviews as $review)
                    <div class="border border-slate-100 rounded-xl p-4 transition-colors hover:border-slate-200">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-slate-800 text-sm">{{ $review->customer?->full_name ?? 'Client' }}</span>
                            <span class="text-xs text-slate-400">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex text-amber-400 text-sm mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($reviewsEnabled)
            @auth('customer')
                @if($hasReviewed)
                    <p class="text-sm text-slate-400">Vous avez déjà donné votre avis sur ce produit.</p>
                @else
                    <form method="POST" action="{{ route('store.reviews.store', $product) }}" class="border border-slate-100 rounded-2xl p-5" x-data="{ rating: 0, hover: 0 }">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-slate-600 mb-2">Votre note</label>
                            <div class="flex gap-1 text-2xl">
                                <template x-for="star in [1,2,3,4,5]" :key="star">
                                    <button type="button" @click="rating = star" @mouseenter="hover = star" @mouseleave="hover = 0"
                                            class="transition-transform hover:scale-110"
                                            :class="star <= (hover || rating) ? 'text-amber-400' : 'text-slate-300'">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-slate-600 mb-1">Commentaire (optionnel)</label>
                            <textarea name="comment" rows="3" class="w-full rounded-lg border-slate-200 text-sm"></textarea>
                        </div>
                        <button type="submit" :disabled="rating === 0"
                                class="rounded-xl px-5 py-2.5 font-semibold text-white disabled:opacity-40 transition-all active:scale-95 hover:brightness-110"
                                style="background: var(--store-button);">
                            Envoyer mon avis
                        </button>
                    </form>
                @endif
            @else
                <p class="text-sm text-slate-400">
                    <a href="{{ route('store.account.login') }}" class="store-link font-medium hover:underline">Connectez-vous</a>
                    pour laisser un avis sur ce produit.
                </p>
            @endauth
        @endif
    </div>

    @if($related->isNotEmpty())
        <div class="mt-16" data-reveal>
            <div class="flex items-center gap-2 mb-6">
                <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
                <h2 class="font-display text-xl font-bold text-slate-900">Produits similaires</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($related as $relatedProduct)
                    @include('storefront.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
