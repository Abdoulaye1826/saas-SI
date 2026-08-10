<footer class="mt-16 text-white text-sm" style="background: var(--store-footer);">
    @php($footerPages = \App\Models\StorePage::published()->where('show_in_footer', true)->orderBy('title')->get())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            <div class="font-display font-bold text-base mb-2 tracking-tight">{{ $settings->name ?: $entreprise->name }}</div>
            @if($settings->slogan)
                <p class="text-white/60 leading-relaxed">{{ $settings->slogan }}</p>
            @endif
        </div>
        <div>
            <div class="font-semibold mb-3 text-white/90 text-xs uppercase tracking-widest">Contact</div>
            <ul class="space-y-2 text-white/60">
                @if($settings->phone)
                    <li class="flex items-center gap-2"><i class="bi bi-telephone"></i>{{ $settings->phone }}</li>
                @endif
                @if($settings->email)
                    <li class="flex items-center gap-2"><i class="bi bi-envelope"></i>{{ $settings->email }}</li>
                @endif
                @if($settings->address)
                    <li class="flex items-center gap-2"><i class="bi bi-geo-alt"></i>{{ $settings->address }}</li>
                @endif
            </ul>
        </div>
        <div>
            @if($settings->opening_hours)
                <div class="font-semibold mb-3 text-white/90 text-xs uppercase tracking-widest">Horaires</div>
                <ul class="space-y-2 text-white/60">
                    @foreach((array) $settings->opening_hours as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if($footerPages->isNotEmpty())
            <div>
                <div class="font-semibold mb-3 text-white/90 text-xs uppercase tracking-widest">Informations</div>
                <ul class="space-y-2 text-white/60">
                    @foreach($footerPages as $footerPage)
                        <li><a href="{{ route('store.pages.show', $footerPage) }}" class="hover:text-white transition-colors">{{ $footerPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="border-t border-white/10 py-5 text-center text-white/40 text-xs store-figures">
        &copy; {{ date('Y') }} {{ $settings->name ?: $entreprise->name }} — Tous droits réservés
    </div>
</footer>
