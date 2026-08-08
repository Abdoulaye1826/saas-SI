@extends('layouts.storefront')

@section('title', ($product->meta_title ?: $product->name).' — '.($settings->name ?: $entreprise->name))
@section('meta_description', $product->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($product->description), 155))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-slate-400 mb-6 flex items-center gap-1">
        <a href="{{ route('store.home') }}" class="hover:text-slate-600">Accueil</a>
        <span>/</span>
        <a href="{{ route('store.products.index') }}" class="hover:text-slate-600">Boutique</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('store.categories.show', $product->category) }}" class="hover:text-slate-600">{{ $product->category->name }}</a>
        @endif
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="aspect-square rounded-2xl bg-slate-50 overflow-hidden">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full flex items-center justify-center text-slate-300">
                    <i class="bi bi-image text-6xl"></i>
                </div>
            @endif
        </div>

        <div>
            @if($product->category)
                <div class="text-xs uppercase tracking-wide text-slate-400 mb-2">{{ $product->category->name }}</div>
            @endif
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-2">{{ $product->name }}</h1>

            @if($product->average_rating !== null)
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= round($product->average_rating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <span class="text-sm text-slate-500">{{ $product->average_rating }}/5 ({{ $product->approvedReviews->count() }} avis)</span>
                </div>
            @endif

            <div class="flex items-baseline gap-3 mb-4">
                @if($product->is_promo && $product->promo_price)
                    <span class="text-3xl font-extrabold" style="color: var(--store-primary);">{{ number_format((float) $product->promo_price, 0, ',', ' ') }} FCFA</span>
                    <span class="text-lg text-slate-400 line-through">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} FCFA</span>
                @else
                    <span class="text-3xl font-extrabold text-slate-900">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>

            @if($product->show_stock)
                <div class="mb-6 text-sm font-medium {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}">
                    <i class="bi {{ $product->stock_quantity > 0 ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                    {{ $product->stock_quantity > 0 ? $product->stock_quantity.' disponible(s)' : 'Rupture de stock' }}
                </div>
            @endif

            @if($product->description)
                <div class="text-sm leading-relaxed text-slate-600 max-w-none mb-8 whitespace-pre-line">{{ $product->description }}</div>
            @endif

            <div class="space-y-3">
                @if($product->allow_order && $product->stock_quantity > 0)
                    <form method="POST" action="{{ route('store.cart.add') }}" class="flex items-center gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @unless($product->tracks_imei)
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                   class="w-20 rounded-lg border-slate-200 text-sm text-center">
                        @endunless
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold text-white transition hover:opacity-90"
                                style="background: var(--store-button);">
                            <i class="bi bi-cart-plus"></i> Ajouter au panier
                        </button>
                    </form>
                @else
                    <button type="button" disabled
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold text-white opacity-50 cursor-not-allowed"
                            style="background: var(--store-button);">
                        <i class="bi bi-cart-x"></i> {{ $product->stock_quantity > 0 ? 'Commande indisponible' : 'Rupture de stock' }}
                    </button>
                @endif

                @if($settings->whatsapp_number && $product->allow_order)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ rawurlencode('Bonjour, je suis intéressé(e) par : '.$product->name) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 transition">
                        <i class="bi bi-whatsapp"></i> Commander sur WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Avis clients ─────────────────────────────────────────── --}}
    <div class="mt-16 max-w-3xl">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Avis clients</h2>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 text-green-800 text-sm px-4 py-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">{{ session('error') }}</div>
        @endif

        @if($product->approvedReviews->isEmpty())
            <p class="text-sm text-slate-400 mb-6">Aucun avis pour ce produit pour le moment.</p>
        @else
            <div class="space-y-4 mb-8">
                @foreach($product->approvedReviews as $review)
                    <div class="border border-slate-100 rounded-xl p-4">
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
                            <p class="text-sm text-slate-600">{{ $review->comment }}</p>
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
                    <form method="POST" action="{{ route('store.reviews.store', $product) }}" class="border border-slate-100 rounded-xl p-5" x-data="{ rating: 0 }">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-slate-600 mb-2">Votre note</label>
                            <div class="flex gap-1 text-2xl">
                                <template x-for="star in [1,2,3,4,5]" :key="star">
                                    <button type="button" @click="rating = star" :class="star <= rating ? 'text-amber-400' : 'text-slate-300'">
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
                                class="rounded-lg px-5 py-2.5 font-semibold text-white disabled:opacity-40"
                                style="background: var(--store-button);">
                            Envoyer mon avis
                        </button>
                    </form>
                @endif
            @else
                <p class="text-sm text-slate-400">
                    <a href="{{ route('store.account.login') }}" class="store-link font-medium">Connectez-vous</a>
                    pour laisser un avis sur ce produit.
                </p>
            @endauth
        @endif
    </div>

    @if($related->isNotEmpty())
        <div class="mt-16">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Produits similaires</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($related as $relatedProduct)
                    @include('storefront.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
