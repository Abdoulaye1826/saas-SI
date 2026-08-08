@php($customer = \Illuminate\Support\Facades\Auth::guard('customer')->user())
<div class="flex items-center justify-between flex-wrap gap-3 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Mon compte</h1>
        <p class="text-sm text-slate-500">Bonjour {{ $customer->full_name }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('store.account.orders.index') }}"
           class="text-sm px-4 py-2 rounded-lg {{ request()->routeIs('store.account.orders.*') ? 'text-white' : 'border border-slate-200 text-slate-600' }}"
           style="{{ request()->routeIs('store.account.orders.*') ? 'background: var(--store-primary);' : '' }}">
            Mes commandes
        </a>
        <a href="{{ route('store.account.profile.edit') }}"
           class="text-sm px-4 py-2 rounded-lg {{ request()->routeIs('store.account.profile.*') ? 'text-white' : 'border border-slate-200 text-slate-600' }}"
           style="{{ request()->routeIs('store.account.profile.*') ? 'background: var(--store-primary);' : '' }}">
            Mon profil
        </a>
        <form method="POST" action="{{ route('store.account.logout') }}">
            @csrf
            <button type="submit" class="text-sm px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">
                Déconnexion
            </button>
        </form>
    </div>
</div>
