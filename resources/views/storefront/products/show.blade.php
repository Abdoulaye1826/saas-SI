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
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">{{ $product->name }}</h1>

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
