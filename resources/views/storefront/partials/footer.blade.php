<footer class="mt-16 text-white text-sm" style="background: var(--store-footer);">
    @php($footerPages = \App\Models\StorePage::published()->where('show_in_footer', true)->orderBy('title')->get())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
            <div class="font-bold text-base mb-2">{{ $settings->name ?: $entreprise->name }}</div>
            @if($settings->slogan)
                <p class="text-white/70">{{ $settings->slogan }}</p>
            @endif
        </div>
        <div>
            <div class="font-semibold mb-2">Contact</div>
            <ul class="space-y-1 text-white/70">
                @if($settings->phone)
                    <li><i class="bi bi-telephone me-1"></i>{{ $settings->phone }}</li>
                @endif
                @if($settings->email)
                    <li><i class="bi bi-envelope me-1"></i>{{ $settings->email }}</li>
                @endif
                @if($settings->address)
                    <li><i class="bi bi-geo-alt me-1"></i>{{ $settings->address }}</li>
                @endif
            </ul>
        </div>
        <div>
            @if($settings->opening_hours)
                <div class="font-semibold mb-2">Horaires</div>
                <ul class="space-y-1 text-white/70">
                    @foreach((array) $settings->opening_hours as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if($footerPages->isNotEmpty())
            <div>
                <div class="font-semibold mb-2">Informations</div>
                <ul class="space-y-1 text-white/70">
                    @foreach($footerPages as $footerPage)
                        <li><a href="{{ route('store.pages.show', $footerPage) }}" class="hover:text-white">{{ $footerPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="border-t border-white/10 py-4 text-center text-white/50 text-xs">
        &copy; {{ date('Y') }} {{ $settings->name ?: $entreprise->name }} — Tous droits réservés
    </div>
</footer>
