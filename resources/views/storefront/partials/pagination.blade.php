{{-- Pagination Tailwind pour la boutique publique — la boutique n'utilise
     pas Bootstrap CSS (voir layouts/storefront.blade.php), donc la vue de
     pagination Bootstrap 5 par défaut (Paginator::useBootstrapFive(), voir
     AppServiceProvider) rendrait des classes non stylées ici. --}}
@if($paginator->hasPages())
<nav class="flex items-center justify-center gap-1.5 text-sm store-figures">
    <a href="{{ $paginator->previousPageUrl() ?? '#' }}"
       class="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 transition-colors {{ $paginator->onFirstPage() ? 'pointer-events-none text-slate-300' : 'text-slate-600 hover:bg-neutral-50 hover:border-slate-300' }}">
        <i class="bi bi-chevron-left"></i>
    </a>

    @for($page = 1; $page <= $paginator->lastPage(); $page++)
        @if($page === $paginator->currentPage())
            <span class="h-9 w-9 flex items-center justify-center rounded-lg text-white font-semibold shadow-sm" style="background: var(--store-primary);">{{ $page }}</span>
        @elseif($page === 1 || $page === $paginator->lastPage() || abs($page - $paginator->currentPage()) <= 1)
            <a href="{{ $paginator->url($page) }}" class="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-neutral-50 hover:border-slate-300 transition-colors">{{ $page }}</a>
        @elseif(abs($page - $paginator->currentPage()) === 2)
            <span class="px-1 text-slate-300">…</span>
        @endif
    @endfor

    <a href="{{ $paginator->nextPageUrl() ?? '#' }}"
       class="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 transition-colors {{ $paginator->hasMorePages() ? 'text-slate-600 hover:bg-neutral-50 hover:border-slate-300' : 'pointer-events-none text-slate-300' }}">
        <i class="bi bi-chevron-right"></i>
    </a>
</nav>
@endif
