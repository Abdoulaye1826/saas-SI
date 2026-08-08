@extends('layouts.storefront')

@section('title', 'Commande confirmée — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600 mb-6">
        <i class="bi bi-check-lg text-3xl"></i>
    </div>
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Merci, votre commande est enregistrée !</h1>
    <p class="text-slate-500 mb-6">
        Numéro de commande : <span class="font-semibold text-slate-800">{{ $order->order_number }}</span><br>
        Nous vous contacterons au {{ $order->guest_phone }} pour confirmer les détails.
    </p>

    <div class="border border-slate-100 rounded-xl p-5 text-left mb-8">
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

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('store.home') }}" class="px-6 py-3 rounded-lg border border-slate-200 text-slate-600 font-medium hover:bg-slate-50">
            Retour à la boutique
        </a>
        @if($settings->whatsapp_number)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ rawurlencode('Bonjour, je viens de passer la commande '.$order->order_number) }}"
               target="_blank" rel="noopener"
               class="px-6 py-3 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700">
                <i class="bi bi-whatsapp me-1"></i> Suivre ma commande sur WhatsApp
            </a>
        @endif
    </div>
</div>
@endsection
