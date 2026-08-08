@extends('layouts.storefront')

@section('title', ($page->meta_title ?: $page->title).' — '.($settings->name ?: $entreprise->name))
@section('meta_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 155))
@section('canonical', route('store.pages.show', $page))
@section('og_title', $page->meta_title ?: $page->title)
@section('og_description', $page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($page->content), 155))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-xs text-slate-400 mb-6 flex items-center gap-1">
        <a href="{{ route('store.home') }}" class="hover:text-slate-600">Accueil</a>
        <span>/</span>
        <span>{{ $page->title }}</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-6">{{ $page->title }}</h1>

    @if($page->content)
        <div class="text-sm leading-relaxed text-slate-600 whitespace-pre-line">{{ $page->content }}</div>
    @else
        <p class="text-sm text-slate-400">Cette page n'a pas encore de contenu.</p>
    @endif
</div>
@endsection
