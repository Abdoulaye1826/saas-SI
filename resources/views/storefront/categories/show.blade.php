@extends('layouts.storefront')

@section('title', $category->name.' — '.($settings->name ?: $entreprise->name))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($category->description), 155))
@section('canonical', route('store.categories.show', $category))
@section('og_title', $category->name)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags($category->description), 155))
@if($category->image_url)
    @section('og_image', $category->image_url)
@endif

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="text-xs text-slate-400 mb-6 flex items-center gap-1">
        <a href="{{ route('store.home') }}" class="hover:text-slate-600">Accueil</a>
        <span>/</span>
        <span>{{ $category->name }}</span>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-sm text-slate-500 mt-1">{{ $category->description }}</p>
            @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('store.products.index') }}" class="text-xs px-3 py-1.5 rounded-full border border-slate-200 text-slate-600 hover:bg-slate-50">Toutes les catégories</a>
            @foreach($categories as $navCategory)
                <a href="{{ route('store.categories.show', $navCategory) }}"
                   class="text-xs px-3 py-1.5 rounded-full {{ $navCategory->id === $category->id ? 'text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                   style="{{ $navCategory->id === $category->id ? 'background: var(--store-primary);' : '' }}">
                    {{ $navCategory->name }}
                </a>
            @endforeach
        </div>
    </div>

    @if($products->isEmpty())
        <div class="text-center text-slate-400 py-24">
            <i class="bi bi-box-seam text-4xl mb-3 block"></i>
            Aucun produit dans cette catégorie pour le moment.
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
                @include('storefront.partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links('storefront.partials.pagination') }}
        </div>
    @endif
</div>
@endsection
