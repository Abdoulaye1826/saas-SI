@extends('layouts.storefront')

@section('title', 'Finaliser la commande — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-2 mb-6">
        <span class="h-5 w-1 rounded-full" style="background: var(--store-primary);"></span>
        <h1 class="font-display text-2xl font-bold text-slate-950">Finaliser la commande</h1>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 text-red-700 text-sm px-4 py-3 border border-red-100">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('store.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="checkoutForm()">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <div class="border border-slate-100 rounded-2xl p-5 bg-white">
                <h2 class="font-display font-semibold text-slate-900 mb-4">Vos coordonnées</h2>
                @if($customer)
                    <div class="flex items-center gap-2 rounded-xl bg-neutral-50 text-sm text-slate-600 px-4 py-3 mb-2">
                        <i class="bi bi-person-check-fill text-lg" style="color: var(--store-primary);"></i>
                        Connecté en tant que <strong>{{ $customer->full_name }}</strong> ({{ $customer->phone }}).
                        <a href="{{ route('store.account.profile.edit') }}" class="store-link hover:underline">Modifier</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Nom complet</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" required class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Téléphone</label>
                            <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" required placeholder="+221 XX XXX XX XX" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 mb-1">Email (optionnel)</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">
                        <a href="{{ route('store.account.login') }}" class="store-link font-medium hover:underline">Déjà un compte ? Connectez-vous</a>
                        pour retrouver vos commandes.
                    </p>
                @endif
            </div>

            <div class="border border-slate-100 rounded-2xl p-5 bg-white">
                <h2 class="font-display font-semibold text-slate-900 mb-4">Livraison</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    @if($settings->delivery_enabled)
                        <label class="flex items-center gap-2.5 text-sm border rounded-xl px-4 py-3 cursor-pointer transition-colors"
                               :class="deliveryMethod === 'home' ? 'border-[--store-primary] bg-[--store-primary-soft]' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="delivery_method" value="home" x-model="deliveryMethod" checked>
                            <span><i class="bi bi-truck me-1"></i> Livraison à domicile</span>
                        </label>
                    @endif
                    @if($settings->pickup_enabled)
                        <label class="flex items-center gap-2.5 text-sm border rounded-xl px-4 py-3 cursor-pointer transition-colors"
                               :class="deliveryMethod === 'pickup' ? 'border-[--store-primary] bg-[--store-primary-soft]' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="delivery_method" value="pickup" x-model="deliveryMethod" {{ $settings->delivery_enabled ? '' : 'checked' }}>
                            <span><i class="bi bi-shop me-1"></i> Retrait en boutique <span class="text-green-600 font-medium">(gratuit)</span></span>
                        </label>
                    @endif
                </div>

                <div x-show="deliveryMethod === 'home'" x-cloak
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Zone</label>
                        <select name="delivery_zone" x-model="deliveryZone" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                            <option value="dakar">Dakar ({{ number_format($settings->delivery_fee_dakar, 0, ',', ' ') }} FCFA)</option>
                            <option value="other">Hors Dakar ({{ number_format($settings->delivery_fee_other, 0, ',', ' ') }} FCFA)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Ville</label>
                        <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Adresse</label>
                        <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Note (optionnel)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-[--store-primary] focus:ring-1 focus:ring-[--store-primary]">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 rounded-xl bg-neutral-50 text-slate-600 text-sm px-4 py-3">
                <i class="bi bi-cash-coin"></i> Paiement à la livraison — vous réglez au moment de la réception.
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="border border-slate-100 rounded-2xl p-5 sticky top-24 bg-white shadow-sm">
                <h2 class="font-display font-semibold text-slate-900 mb-4">Récapitulatif</h2>
                <ul class="space-y-2 text-sm text-slate-600 mb-4">
                    @foreach($items as $line)
                        <li class="flex justify-between">
                            <span class="line-clamp-1 pr-2">{{ $line['quantity'] }} × {{ $line['product']->name }}</span>
                            <span class="store-figures">{{ number_format($line['line_total'], 0, ',', ' ') }} FCFA</span>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 pt-3 space-y-1 text-sm store-figures">
                    <div class="flex justify-between text-slate-500">
                        <span>Sous-total</span>
                        <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Livraison</span>
                        <span x-text="deliveryFeeLabel"></span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-950 text-base pt-2 font-display">
                        <span>Total</span>
                        <span x-text="totalLabel"></span>
                    </div>
                </div>

                <button type="submit" class="w-full mt-5 rounded-xl py-3.5 font-semibold text-white transition-all active:scale-95 hover:brightness-110 shadow-lg store-glow" style="background: var(--store-button);">
                    Confirmer la commande
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function checkoutForm() {
        return {
            deliveryMethod: {!! $settings->delivery_enabled ? "'home'" : "'pickup'" !!},
            deliveryZone: 'dakar',
            subtotal: {{ (float) $subtotal }},
            fees: {
                dakar: {{ (float) $settings->delivery_fee_dakar }},
                other: {{ (float) $settings->delivery_fee_other }},
                pickup: 0,
            },
            freeThreshold: {{ $settings->free_delivery_threshold !== null ? (float) $settings->free_delivery_threshold : 'null' }},
            get deliveryFee() {
                if (this.deliveryMethod === 'pickup') return 0;
                if (this.freeThreshold !== null && this.subtotal >= this.freeThreshold) return 0;
                return this.deliveryZone === 'dakar' ? this.fees.dakar : this.fees.other;
            },
            get deliveryFeeLabel() {
                return this.deliveryFee === 0 ? 'Gratuit' : new Intl.NumberFormat('fr-FR').format(this.deliveryFee) + ' FCFA';
            },
            get totalLabel() {
                return new Intl.NumberFormat('fr-FR').format(this.subtotal + this.deliveryFee) + ' FCFA';
            },
        };
    }
</script>
@endpush
