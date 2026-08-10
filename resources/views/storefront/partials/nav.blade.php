@php
    $navCategories = \App\Models\Category::onStore()->limit(6)->get();
    $navMenus = \App\Models\StoreMenu::active()->get();
    $cartCount = app(\App\Services\CartService::class)->count();
@endphp
<header
    class="sticky top-0 z-40 transition-shadow duration-300"
    :class="scrolled ? 'shadow-lg' : 'shadow-sm'"
    style="background: var(--store-navbar);"
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => scrolled = window.scrollY > 8, { passive: true })"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('store.home') }}" class="flex items-center gap-2.5 text-white font-display font-bold text-lg tracking-tight group">
                @if($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->name }}" class="h-9 w-9 rounded-lg object-cover ring-1 ring-white/20 transition-transform group-hover:scale-105">
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/15 transition-transform group-hover:scale-105">
                        <i class="bi bi-shop text-white"></i>
                    </span>
                @endif
                <span>{{ $settings->name ?: $entreprise->name }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('store.home') }}" class="relative px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition-colors">Accueil</a>
                <a href="{{ route('store.products.index') }}" class="relative px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition-colors">Boutique</a>
                @foreach($navCategories as $navCategory)
                    <a href="{{ route('store.categories.show', $navCategory) }}" class="relative px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition-colors">{{ $navCategory->name }}</a>
                @endforeach
                @foreach($navMenus as $navMenu)
                    <a href="{{ $navMenu->url }}" @if($navMenu->opens_new_tab) target="_blank" rel="noopener" @endif class="relative px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10 transition-colors">{{ $navMenu->label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                @if($settings->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}" target="_blank" rel="noopener"
                       class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-white/85 hover:text-white px-2 py-1.5 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="bi bi-whatsapp"></i> Nous contacter
                    </a>
                @endif
                @auth('customer')
                    <a href="{{ route('store.account.orders.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-white/85 hover:text-white px-2 py-1.5 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="bi bi-person-circle"></i> {{ Str::before(Auth::guard('customer')->user()->full_name, ' ') }}
                    </a>
                @else
                    <a href="{{ route('store.account.login') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-white/85 hover:text-white px-2 py-1.5 rounded-lg hover:bg-white/10 transition-colors">
                        <i class="bi bi-person"></i> Connexion
                    </a>
                @endauth
                <a href="{{ route('store.cart.show') }}" class="relative text-white/85 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors" aria-label="Panier">
                    <i class="bi bi-cart3 text-xl"></i>
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 h-4.5 w-4.5 min-w-[1.125rem] px-0.5 flex items-center justify-center rounded-full bg-white text-[10px] font-bold animate-pop store-figures" style="color: var(--store-navbar);">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                <button type="button" class="md:hidden text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Menu">
                    <i class="bi text-2xl" :class="mobileOpen ? 'bi-x-lg' : 'bi-list'"></i>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="md:hidden pb-4 flex flex-col gap-1 text-sm font-medium">
            <a href="{{ route('store.home') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">Accueil</a>
            <a href="{{ route('store.products.index') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">Boutique</a>
            @foreach($navCategories as $navCategory)
                <a href="{{ route('store.categories.show', $navCategory) }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ $navCategory->name }}</a>
            @endforeach
            @foreach($navMenus as $navMenu)
                <a href="{{ $navMenu->url }}" @if($navMenu->opens_new_tab) target="_blank" rel="noopener" @endif class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">{{ $navMenu->label }}</a>
            @endforeach
            @auth('customer')
                <a href="{{ route('store.account.orders.index') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">Mon compte</a>
            @else
                <a href="{{ route('store.account.login') }}" class="px-3 py-2 rounded-lg text-white/85 hover:text-white hover:bg-white/10">Connexion</a>
            @endauth
        </div>
    </div>
</header>
