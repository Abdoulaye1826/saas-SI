{{-- Carte produit réutilisée sur l'accueil, le catalogue et les pages
     catégorie. Jamais d'IMEI ici — uniquement stock_quantity si show_stock. --}}
<a href="{{ route('store.products.show', $product) }}" class="group block rounded-xl border border-slate-100 hover:shadow-lg transition overflow-hidden bg-white">
    <div class="aspect-square bg-slate-50 relative overflow-hidden">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy"
                 class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="h-full w-full flex items-center justify-center text-slate-300">
                <i class="bi bi-image text-4xl"></i>
            </div>
        @endif

        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if($product->is_new)
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-900 text-white">Nouveau</span>
            @endif
            @if($product->is_promo && $product->promo_price)
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full text-white" style="background: var(--store-primary);">Promo</span>
            @endif
        </div>
    </div>

    <div class="p-3">
        @if($product->category)
            <div class="text-[11px] uppercase tracking-wide text-slate-400 mb-1">{{ $product->category->name }}</div>
        @endif
        <div class="text-sm font-medium text-slate-800 line-clamp-2 min-h-[2.5rem]">{{ $product->name }}</div>

        <div class="mt-2 flex items-baseline gap-2">
            @if($product->is_promo && $product->promo_price)
                <span class="text-base font-bold" style="color: var(--store-primary);">{{ number_format((float) $product->promo_price, 0, ',', ' ') }} FCFA</span>
                <span class="text-xs text-slate-400 line-through">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} FCFA</span>
            @else
                <span class="text-base font-bold text-slate-900">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} FCFA</span>
            @endif
        </div>

        @if($product->show_stock)
            <div class="mt-1 text-xs {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $product->stock_quantity > 0 ? $product->stock_quantity.' disponible(s)' : 'Rupture de stock' }}
            </div>
        @endif
    </div>
</a>
