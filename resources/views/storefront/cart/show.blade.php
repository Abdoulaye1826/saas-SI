@extends('layouts.storefront')

@section('title', 'Panier — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Mon panier</h1>

    @if(session('error'))
        <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="text-center text-slate-400 py-24">
            <i class="bi bi-cart-x text-4xl mb-3 block"></i>
            Votre panier est vide.
            <div class="mt-4">
                <a href="{{ route('store.products.index') }}" class="store-link font-medium">Découvrir nos produits →</a>
            </div>
        </div>
    @else
        <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
            @foreach($items as $line)
                @php($product = $line['product'])
                <div class="flex items-center gap-4 p-4">
                    <div class="h-16 w-16 rounded-lg bg-slate-50 overflow-hidden flex-shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-slate-300"><i class="bi bi-image"></i></div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <a href="{{ route('store.products.show', $product) }}" class="font-medium text-slate-800 hover:underline line-clamp-1">{{ $product->name }}</a>
                        <div class="text-sm text-slate-500 mt-0.5">{{ number_format($product->effective_price, 0, ',', ' ') }} FCFA</div>
                    </div>

                    <form method="POST" action="{{ route('store.cart.update', $product) }}" class="flex items-center gap-1">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1"
                               max="{{ $product->tracks_imei ? 1 : max(1, $product->stock_quantity) }}"
                               {{ $product->tracks_imei ? 'readonly' : '' }}
                               class="w-16 rounded-lg border-slate-200 text-sm text-center">
                        <button type="submit" class="text-xs px-2 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </form>

                    <div class="w-24 text-right font-semibold text-slate-900">{{ number_format($line['line_total'], 0, ',', ' ') }} FCFA</div>

                    <form method="POST" action="{{ route('store.cart.remove', $product) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-red-500" title="Retirer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end mt-6">
            <div class="w-full sm:w-80 space-y-2">
                <div class="flex justify-between text-sm text-slate-500">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <p class="text-xs text-slate-400">Les frais de livraison sont calculés à l'étape suivante.</p>
                <a href="{{ route('store.checkout.show') }}"
                   class="block text-center w-full rounded-lg py-3 font-semibold text-white mt-3"
                   style="background: var(--store-button);">
                    Passer la commande
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
