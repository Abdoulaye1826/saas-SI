@extends('layouts.storefront')

@section('title', 'Commande '.$order->order_number.' — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('storefront.account._nav')

    <a href="{{ route('store.account.orders.index') }}" class="text-sm store-link mb-4 inline-block">← Retour à mes commandes</a>

    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <h2 class="text-xl font-bold text-slate-900">Commande {{ $order->order_number }}</h2>
        <span class="inline-block text-xs px-3 py-1 rounded-full text-white" style="background: var(--store-primary);">
            {{ $order->status->label() }}
        </span>
    </div>

    {{-- Suivi simple : les statuts du cahier des charges dans l'ordre,
         ceux déjà atteints en surbrillance. --}}
    @php
        $steps = ['confirmed' => 'Confirmée', 'preparing' => 'En préparation', 'ready' => 'Prête', 'shipped' => 'Expédiée', 'delivered' => 'Livrée'];
        $reached = array_search($order->status->value, array_keys($steps), true);
    @endphp
    @if($order->status->value !== 'cancelled')
        <div class="flex items-center justify-between mb-8 overflow-x-auto">
            @foreach($steps as $key => $label)
                @php($isReached = $reached !== false && array_search($key, array_keys($steps), true) <= $reached)
                <div class="flex-1 text-center min-w-[80px]">
                    <div class="mx-auto h-2 rounded-full mb-2" style="background: {{ $isReached ? 'var(--store-primary)' : '#e2e8f0' }};"></div>
                    <div class="text-xs {{ $isReached ? 'text-slate-800 font-medium' : 'text-slate-400' }}">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="border border-slate-100 rounded-xl p-5 mb-6">
        <ul class="divide-y divide-slate-100 text-sm">
            @foreach($order->items as $item)
                <li class="flex justify-between py-2">
                    <span>{{ $item->quantity }} × {{ $item->product_name }}</span>
                    <span>{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-slate-100 mt-2 pt-2 space-y-1 text-sm">
            <div class="flex justify-between text-slate-500"><span>Sous-total</span><span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
            <div class="flex justify-between text-slate-500"><span>Livraison</span><span>{{ $order->delivery_fee > 0 ? number_format($order->delivery_fee, 0, ',', ' ').' FCFA' : 'Gratuit' }}</span></div>
            <div class="flex justify-between font-bold text-slate-900"><span>Total</span><span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="border border-slate-100 rounded-xl p-5">
            <h3 class="font-semibold text-slate-800 mb-2 text-sm">Livraison</h3>
            <p class="text-sm text-slate-600">{{ $order->delivery_method === 'pickup' ? 'Retrait en boutique' : 'Livraison à domicile' }}</p>
            @if($order->delivery_method !== 'pickup')
                <p class="text-sm text-slate-500">{{ $order->delivery_address }}, {{ $order->delivery_city }}</p>
            @endif
            @if($order->assignedDriver)
                <p class="text-sm text-slate-500 mt-1">Livreur : {{ $order->assignedDriver->name }}</p>
            @endif
        </div>
        <div class="border border-slate-100 rounded-xl p-5">
            <h3 class="font-semibold text-slate-800 mb-2 text-sm">Paiement</h3>
            <p class="text-sm text-slate-600">Paiement à la livraison</p>
            @if($order->sale?->invoice)
                <a href="{{ route('store.account.orders.invoice', $order) }}" class="store-link text-sm font-medium inline-flex items-center gap-1 mt-2">
                    <i class="bi bi-download"></i> Télécharger la facture
                </a>
            @endif
        </div>
    </div>

    @if($settings->whatsapp_number)
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ rawurlencode('Bonjour, je vous contacte au sujet de ma commande '.$order->order_number) }}"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700">
            <i class="bi bi-whatsapp"></i> Contacter la boutique
        </a>
    @endif
</div>
@endsection
