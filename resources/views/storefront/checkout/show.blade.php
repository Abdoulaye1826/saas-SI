@extends('layouts.storefront')

@section('title', 'Finaliser la commande — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Finaliser la commande</h1>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('store.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="checkoutForm()">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <div class="border border-slate-100 rounded-xl p-5">
                <h2 class="font-semibold text-slate-800 mb-4">Vos coordonnées</h2>
                @if($customer)
                    <div class="rounded-lg bg-slate-50 text-sm text-slate-600 px-4 py-3 mb-2">
                        Connecté en tant que <strong>{{ $customer->full_name }}</strong> ({{ $customer->phone }}).
                        <a href="{{ route('store.account.profile.edit') }}" class="store-link">Modifier mes informations</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Nom complet</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" required class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Téléphone</label>
                            <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" required placeholder="+221 XX XXX XX XX" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 mb-1">Email (optionnel)</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-lg border-slate-200 text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">
                        <a href="{{ route('store.account.login') }}" class="store-link font-medium">Déjà un compte ? Connectez-vous</a>
                        pour retrouver vos commandes.
                    </p>
                @endif
            </div>

            <div class="border border-slate-100 rounded-xl p-5">
                <h2 class="font-semibold text-slate-800 mb-4">Livraison</h2>

                <div class="space-y-2 mb-4">
                    @if($settings->delivery_enabled)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="delivery_method" value="home" x-model="deliveryMethod" checked>
                            <span>Livraison à domicile</span>
                        </label>
                    @endif
                    @if($settings->pickup_enabled)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="delivery_method" value="pickup" x-model="deliveryMethod" {{ $settings->delivery_enabled ? '' : 'checked' }}>
                            <span>Retrait en boutique (gratuit)</span>
                        </label>
                    @endif
                </div>

                <div x-show="deliveryMethod === 'home'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Zone</label>
                        <select name="delivery_zone" x-model="deliveryZone" class="w-full rounded-lg border-slate-200 text-sm">
                            <option value="dakar">Dakar ({{ number_format($settings->delivery_fee_dakar, 0, ',', ' ') }} FCFA)</option>
                            <option value="other">Hors Dakar ({{ number_format($settings->delivery_fee_other, 0, ',', ' ') }} FCFA)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Ville</label>
                        <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-1">Adresse</label>
                        <input type="text" name="delivery_address" value="{{ old('delivery_address') }}" class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Note (optionnel)</label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-200 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="rounded-lg bg-slate-50 text-slate-600 text-sm px-4 py-3">
                <i class="bi bi-cash-coin me-1"></i> Paiement à la livraison — vous réglez au moment de la réception.
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="border border-slate-100 rounded-xl p-5 sticky top-24">
                <h2 class="font-semibold text-slate-800 mb-4">Récapitulatif</h2>
                <ul class="space-y-2 text-sm text-slate-600 mb-4">
                    @foreach($items as $line)
                        <li class="flex justify-between">
                            <span class="line-clamp-1 pr-2">{{ $line['quantity'] }} × {{ $line['product']->name }}</span>
                            <span>{{ number_format($line['line_total'], 0, ',', ' ') }} FCFA</span>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 pt-3 space-y-1 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Sous-total</span>
                        <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Livraison</span>
                        <span x-text="deliveryFeeLabel"></span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-base pt-2">
                        <span>Total</span>
                        <span x-text="totalLabel"></span>
                    </div>
                </div>

                <button type="submit" class="w-full mt-5 rounded-lg py-3 font-semibold text-white" style="background: var(--store-button);">
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
