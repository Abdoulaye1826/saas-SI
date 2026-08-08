@extends('layouts.storefront')

@section('title', 'Mes commandes — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('storefront.account._nav')

    @if($orders->isEmpty())
        <div class="text-center text-slate-400 py-16">
            <i class="bi bi-bag text-4xl mb-3 block"></i>
            Vous n'avez pas encore passé de commande.
            <div class="mt-4">
                <a href="{{ route('store.products.index') }}" class="store-link font-medium">Découvrir nos produits →</a>
            </div>
        </div>
    @else
        <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
            @foreach($orders as $order)
                <a href="{{ route('store.account.orders.show', $order) }}" class="flex items-center justify-between gap-4 p-4 hover:bg-slate-50">
                    <div>
                        <div class="font-medium text-slate-800">{{ $order->order_number }}</div>
                        <div class="text-xs text-slate-400">{{ $order->created_at->format('d/m/Y') }} · {{ $order->items->count() }} article(s)</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-slate-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</div>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full text-white" style="background: var(--store-primary);">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links('storefront.partials.pagination') }}</div>
    @endif
</div>
@endsection
