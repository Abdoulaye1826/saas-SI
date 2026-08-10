{{-- Rangée de produits défilable horizontalement avec flèches de
     navigation, réutilisée pour "Produits populaires", "Nouveautés",
     "Promotions" (accueil) et "Produits similaires" (fiche produit) — pas
     de wrap à la ligne suivante au-delà de 4 produits.
     Attend : $products (Collection de Product). --}}
<div class="relative"
     x-data="{
        atStart: true,
        atEnd: false,
        updateEdges() {
            const el = this.$refs.track;
            this.atStart = el.scrollLeft <= 4;
            this.atEnd = el.scrollLeft >= el.scrollWidth - el.clientWidth - 4;
        },
        scrollByPage(direction) {
            const el = this.$refs.track;
            el.scrollBy({ left: direction * el.clientWidth * 0.9, behavior: 'smooth' });
        },
     }"
     x-init="$nextTick(() => updateEdges())">
    <button type="button" @click="scrollByPage(-1)" x-show="!atStart" x-cloak
            class="absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 z-10 h-9 w-9 sm:h-10 sm:w-10 flex items-center justify-center rounded-full bg-white text-slate-700 shadow-lg border border-slate-100 hover:scale-105 active:scale-95 transition-transform"
            aria-label="Précédent">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div x-ref="track" @scroll.passive="updateEdges()"
         class="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar pb-1 -mx-1 px-1">
        @foreach($products as $product)
            <div class="flex-none snap-start w-[46%] sm:w-[31%] lg:w-[23.5%]">
                @include('storefront.partials.product-card', ['product' => $product])
            </div>
        @endforeach
    </div>

    <button type="button" @click="scrollByPage(1)" x-show="!atEnd" x-cloak
            class="absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 z-10 h-9 w-9 sm:h-10 sm:w-10 flex items-center justify-center rounded-full bg-white text-slate-700 shadow-lg border border-slate-100 hover:scale-105 active:scale-95 transition-transform"
            aria-label="Suivant">
        <i class="bi bi-chevron-right"></i>
    </button>
</div>
