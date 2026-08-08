{{-- Pagination Tailwind pour la boutique publique — la boutique n'utilise
     pas Bootstrap CSS (voir layouts/storefront.blade.php), donc la vue de
     pagination Bootstrap 5 par défaut (Paginator::useBootstrapFive(), voir
     AppServiceProvider) rendrait des classes non stylées ici. --}}
@if($paginator->hasPages())
<nav class="flex items-center justify-center gap-1 text-sm">
    <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
       class="px-3 py-1.5 rounded-lg border border-slate-200 {{ $paginator->onFirstPage() ? 'pointer-events-none text-slate-300' : 'text-slate-600 hover:bg-slate-50' }}">
        <i class="bi bi-chevron-left"></i>
    </a>

    @for($page = 1; $page <= $paginator->lastPage(); $page++)
        @if($page === $paginator->currentPage())
            <span class="px-3 py-1.5 rounded-lg text-white font-semibold" style="background: var(--store-primary);">{{ $page }}</span>
        @elseif($page === 1 || $page === $paginator->lastPage() || abs($page - $paginator->currentPage()) <= 1)
            <a href="{{ $paginator->url($page) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">{{ $page }}</a>
        @elseif(abs($page - $paginator->currentPage()) === 2)
            <span class="px-2 text-slate-300">…</span>
        @endif
    @endfor

    <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
       class="px-3 py-1.5 rounded-lg border border-slate-200 {{ $paginator->hasMorePages() ? 'text-slate-600 hover:bg-slate-50' : 'pointer-events-none text-slate-300' }}">
        <i class="bi bi-chevron-right"></i>
    </a>
</nav>
@endif
