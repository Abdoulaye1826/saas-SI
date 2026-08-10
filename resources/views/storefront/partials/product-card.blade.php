@php
    // $dark : utilisé uniquement dans le bandeau "Promotions" (fond
    // gaming-arena sombre, voir home.blade.php) — jamais sur le catalogue
    // ni les pages catégorie, où le fond clair reste la référence pour la
    // lisibilité des photos produit (priorité du brief : lisibilité avant
    // esthétique).
    $dark = $dark ?? false;
@endphp
{{-- Carte produit réutilisée sur l'accueil, le catalogue et les pages
     catégorie. Jamais d'IMEI ici — uniquement stock_quantity si show_stock.
     Structure : un lien plein-carte (clic = fiche produit) + un bouton
     "Ajouter au panier" rapide superposé, visible en permanence sur mobile
     (pas de survol tactile) et en apparition douce au survol sur desktop. --}}
<div class="group relative rounded-2xl overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1
            {{ $dark ? 'bg-neutral-900 border border-white/10 hover:border-transparent hover:gaming-ring' : 'bg-white border border-slate-100 hover:border-transparent hover:shadow-xl' }}">
    <a href="{{ route('store.products.show', $product) }}" class="absolute inset-0 z-10" aria-label="{{ $product->name }}"></a>

    <div class="aspect-square {{ $dark ? 'bg-neutral-800' : 'bg-neutral-50' }} relative overflow-hidden">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
        @else
            <div class="h-full w-full flex items-center justify-center {{ $dark ? 'text-white/20' : 'text-slate-300' }}">
                <i class="bi bi-controller text-4xl"></i>
            </div>
        @endif

        {{-- Voile au survol : renforce la profondeur de l'image (brief §5). --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/25 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1 z-20">
            @if($product->is_new)
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-full bg-slate-900/90 text-white backdrop-blur-sm">Nouveau</span>
            @endif
            @if($product->is_promo && $product->promo_price && (float) $product->sale_price > 0)
                @php($discountPct = round((1 - ((float) $product->promo_price / (float) $product->sale_price)) * 100))
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-1 rounded-full text-white backdrop-blur-sm animate-glow-pulse" style="background: var(--store-primary);">
                    -{{ $discountPct }}%
                </span>
            @endif
        </div>

        @if($product->show_stock && $product->stock_quantity <= 0)
            <div class="absolute inset-0 {{ $dark ? 'bg-neutral-950/70' : 'bg-white/70' }} backdrop-blur-[1px] flex items-center justify-center">
                <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-900 text-white">Rupture de stock</span>
            </div>
        @endif

        {{-- Ajout rapide au panier : superposé à l'image, au-dessus du lien
             plein-carte (z-20 > z-10) pour rester cliquable. Visible en
             permanence sur mobile (pas de notion de survol au tactile),
             en apparition douce au survol sur desktop (brief §5/§11). --}}
        @if($product->allow_order && !$product->tracks_imei && $product->stock_quantity > 0)
            <form method="POST" action="{{ route('store.cart.add') }}"
                  class="absolute bottom-2.5 right-2.5 z-20 sm:opacity-0 sm:translate-y-1 sm:group-hover:opacity-100 sm:group-hover:translate-y-0 transition-all duration-300">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                        class="h-9 w-9 flex items-center justify-center rounded-full text-white shadow-lg transition-transform active:scale-90 hover:scale-110"
                        style="background: var(--store-button);"
                        title="Ajouter au panier" aria-label="Ajouter {{ $product->name }} au panier">
                    <i class="bi bi-cart-plus"></i>
                </button>
            </form>
        @endif
    </div>

    <div class="p-3.5">
        @if($product->category)
            <div class="text-[10px] font-semibold uppercase tracking-wider {{ $dark ? 'text-white/40' : 'text-slate-400' }} mb-1">{{ $product->category->name }}</div>
        @endif
        <div class="text-sm font-medium {{ $dark ? 'text-white/90 group-hover:text-white' : 'text-slate-800 group-hover:text-slate-950' }} line-clamp-2 min-h-[2.5rem] leading-snug transition-colors">{{ $product->name }}</div>

        <div class="mt-2.5 flex items-baseline gap-2 font-display store-figures">
            @if($product->is_promo && $product->promo_price)
                <span class="text-lg font-bold" style="color: var(--store-primary);">{{ number_format((float) $product->promo_price, 0, ',', ' ') }} <span class="text-xs font-semibold">FCFA</span></span>
                <span class="text-xs {{ $dark ? 'text-white/40' : 'text-slate-400' }} line-through">{{ number_format((float) $product->sale_price, 0, ',', ' ') }}</span>
            @else
                <span class="text-lg font-bold {{ $dark ? 'text-white' : 'text-slate-900' }}">{{ number_format((float) $product->sale_price, 0, ',', ' ') }} <span class="text-xs font-semibold {{ $dark ? 'text-white/50' : 'text-slate-500' }}">FCFA</span></span>
            @endif
        </div>

        @if($product->show_stock && $product->stock_quantity > 0)
            <div class="mt-1.5 flex items-center gap-1.5 text-xs {{ $dark ? 'text-green-400' : 'text-green-700' }}">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                {{ $product->stock_quantity }} disponible(s)
            </div>
        @endif
    </div>
</div>
