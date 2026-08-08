@php
    $navCategories = \App\Models\Category::onStore()->limit(6)->get();
    $navMenus = \App\Models\StoreMenu::active()->get();
@endphp
<header class="sticky top-0 z-40 shadow-sm" style="background: var(--store-navbar);" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('store.home') }}" class="flex items-center gap-2 text-white font-bold text-lg">
                @if($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->name }}" class="h-9 w-9 rounded object-cover">
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded bg-white/15">
                        <i class="bi bi-shop text-white"></i>
                    </span>
                @endif
                <span>{{ $settings->name ?: $entreprise->name }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('store.home') }}" class="text-white/90 hover:text-white">Accueil</a>
                <a href="{{ route('store.products.index') }}" class="text-white/90 hover:text-white">Boutique</a>
                @foreach($navCategories as $navCategory)
                    <a href="{{ route('store.categories.show', $navCategory) }}" class="text-white/90 hover:text-white">{{ $navCategory->name }}</a>
                @endforeach
                @foreach($navMenus as $navMenu)
                    <a href="{{ $navMenu->url }}" @if($navMenu->opens_new_tab) target="_blank" rel="noopener" @endif class="text-white/90 hover:text-white">{{ $navMenu->label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-4">
                @if($settings->whatsapp_number)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}" target="_blank" rel="noopener"
                       class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-white/90 hover:text-white">
                        <i class="bi bi-whatsapp"></i> Nous contacter
                    </a>
                @endif
                @auth('customer')
                    <a href="{{ route('store.account.orders.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-white/90 hover:text-white">
                        <i class="bi bi-person-circle"></i> {{ Auth::guard('customer')->user()->full_name }}
                    </a>
                @else
                    <a href="{{ route('store.account.login') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-white/90 hover:text-white">
                        <i class="bi bi-person"></i> Connexion
                    </a>
                @endauth
                <a href="{{ route('store.cart.show') }}" class="relative text-white/90 hover:text-white">
                    <i class="bi bi-cart3 text-xl"></i>
                    @php($cartCount = app(\App\Services\CartService::class)->count())
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 h-4 w-4 flex items-center justify-center rounded-full bg-white text-[10px] font-bold" style="color: var(--store-navbar);">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                <button type="button" class="md:hidden text-white" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak class="md:hidden pb-4 flex flex-col gap-2 text-sm font-medium">
            <a href="{{ route('store.home') }}" class="text-white/90 hover:text-white">Accueil</a>
            <a href="{{ route('store.products.index') }}" class="text-white/90 hover:text-white">Boutique</a>
            @foreach($navCategories as $navCategory)
                <a href="{{ route('store.categories.show', $navCategory) }}" class="text-white/90 hover:text-white">{{ $navCategory->name }}</a>
            @endforeach
            @foreach($navMenus as $navMenu)
                <a href="{{ $navMenu->url }}" @if($navMenu->opens_new_tab) target="_blank" rel="noopener" @endif class="text-white/90 hover:text-white">{{ $navMenu->label }}</a>
            @endforeach
            @auth('customer')
                <a href="{{ route('store.account.orders.index') }}" class="text-white/90 hover:text-white">Mon compte</a>
            @else
                <a href="{{ route('store.account.login') }}" class="text-white/90 hover:text-white">Connexion</a>
            @endauth
        </div>
    </div>
</header>
