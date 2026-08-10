{{-- Carte produit réutilisée sur l'accueil, le catalogue et les pages
     catégorie. Jamais d'IMEI ici — uniquement stock_quantity si show_stock. --}}
<a href="{{ route('store.products.show', $product) }}"
   class="group relative block rounded-2xl border border-slate-100 bg-white overflow-hidden
          transition-all duration-300 ease-out
          hover:-translate-y-1 hover:border-transparent hover:shadow-xl">
    <div class="aspect-square bg-neutral-50 relative overflow-hidden">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
        @else
            <div class="h-full w-full flex items-center justify-center text-slate-300">
                <i class="bi bi-controller text-4xl"></i>
            </div>
        @endif

        {{-- Voile au survol : suggère l'action sans dupliquer le bouton
             "Ajouter au panier" de la fiche produit (un clic ici ouvre la
             fiche, cohérent avec le reste de la carte). --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/15 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1">
            @if($product->is_new)
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-slate-900/90 text-white backdrop-blur-sm">Nouveau</span>
            @endif
            @if($product->is_promo && $product->promo_price && (float) $product->sale_price > 0)
                @php($discountPct = round((1 - ((float) $product->promo_price / (float) $product->sale_price)) * 100))
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white backdrop-blur-sm" style="background: var(--store-primary);">
                    -{{ $discountPct }}%
                </span>
            @endif
        </div>

        @if($product->show_stock && $product->stock_quantity <= 0)
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-900 text-white">Rupture de stock</span>
            </div>
        @endif
    </div>

    <div class="p-3.5">
        @if($product->category)
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">{{ $product->category->name }}</div>
        @endif
        <div class="text-sm font-medium text-slate-800 line-clamp-2 min-h-[2.5rem] leading-snug transition-colors group-hover:text-slate-950">{{ $product->name }}</div>

        <div class="mt-2.5 flex items-baseline gap-2 font-display store-figures">
            @if($product->is_promo && $product->promo_price)
                <span class="text-lg font-bold" style="color: var(--store-primary);">{{ number_format((float) $product->promo_price, 0, ',', ' ') }} <span class="text-xs font-semibold">FCFA</span></span>
                <span class="text-xs text-slate-400 line-through">{{ number_format((float) $product->sale_price, 0, ',', ' ') }}</span>
            @else
                <span class="text-lg font-bold text-slate-900">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} <span class="text-xs font-semibold text-slate-500">FCFA</span></span>
            @endif
        </div>

        @if($product->show_stock && $product->stock_quantity > 0)
            <div class="mt-1.5 flex items-center gap-1.5 text-xs text-green-700">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                {{ $product->stock_quantity }} disponible(s)
            </div>
        @endif
    </div>
</a>
