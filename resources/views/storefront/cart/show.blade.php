@extends('layouts.storefront')

@section('title', 'Panier — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-2 mb-6">
        <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
        <h1 class="font-display text-2xl font-bold text-slate-950">Mon panier</h1>
    </div>

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 rounded-xl bg-red-50 text-red-700 text-sm px-4 py-3 border border-red-100"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="text-center text-slate-400 py-24">
            <i class="bi bi-cart-x text-5xl mb-4 block"></i>
            <p class="mb-4">Votre panier est vide.</p>
            <a href="{{ route('store.products.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-white transition-all active:scale-95 hover:brightness-110" style="background: var(--store-button);">
                Découvrir nos produits <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @else
        <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden bg-white">
            @foreach($items as $line)
                @php($product = $line['product'])
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-4 p-4 transition-colors hover:bg-neutral-50/60">
                    <div class="h-16 w-16 rounded-xl bg-neutral-50 overflow-hidden flex-shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-slate-300"><i class="bi bi-controller"></i></div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-[10rem]">
                        <a href="{{ route('store.products.show', $product) }}" class="font-medium text-slate-800 hover:text-slate-950 hover:underline line-clamp-1 transition-colors">{{ $product->name }}</a>
                        <div class="text-sm text-slate-500 mt-0.5 store-figures">{{ number_format($product->effective_price, 0, ',', ' ') }} FCFA</div>
                    </div>

                    <form method="POST" action="{{ route('store.cart.update', $product) }}" class="flex items-center gap-1.5">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="1"
                               max="{{ $product->tracks_imei ? 1 : max(1, $product->stock_quantity) }}"
                               {{ $product->tracks_imei ? 'readonly' : '' }}
                               class="w-16 rounded-lg border-slate-200 text-sm text-center store-figures focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                        <button type="submit" class="text-xs h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-neutral-50 hover:border-slate-300 transition-colors" title="Mettre à jour">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </form>

                    <div class="w-24 text-right font-semibold text-slate-900 store-figures">{{ number_format($line['line_total'], 0, ',', ' ') }} FCFA</div>

                    <form method="POST" action="{{ route('store.cart.remove', $product) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors h-8 w-8 flex items-center justify-center" title="Retirer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end mt-6">
            <div class="w-full sm:w-80 space-y-2 bg-white border border-slate-100 rounded-2xl p-5">
                <div class="flex justify-between text-sm text-slate-500 store-figures">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <p class="text-xs text-slate-400">Les frais de livraison sont calculés à l'étape suivante.</p>
                <a href="{{ route('store.checkout.show') }}"
                   class="flex items-center justify-center gap-2 w-full rounded-xl py-3 font-semibold text-white mt-3 transition-all active:scale-95 hover:brightness-110 shadow-lg store-glow"
                   style="background: var(--store-button);">
                    Passer la commande <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
