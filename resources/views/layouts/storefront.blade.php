<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $settings->name ?: $entreprise->name)</title>
    <meta name="description" content="@yield('meta_description', $settings->meta_description ?: $settings->description)">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph / Twitter Card. Chaque vue peut définir ses propres
         sections og_title/og_description/og_image/og_type (voir
         storefront/products/show.blade.php pour un exemple) ; à défaut,
         retombe sur le nom et la description de la boutique. --}}
    <meta property="og:site_name" content="{{ $settings->name ?: $entreprise->name }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('og_title', $settings->name ?: $entreprise->name)">
    <meta property="og:description" content="@yield('og_description', $settings->meta_description ?: $settings->description)">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="@yield('og_image')">
    @elseif($settings->og_image_url ?: ($settings->logo_url ?? $entreprise->logo_url ?? null))
        <meta property="og:image" content="{{ $settings->og_image_url ?: ($settings->logo_url ?? $entreprise->logo_url) }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $settings->og_image_url ?: ($settings->logo_url ?? $entreprise->logo_url) }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <meta name="twitter:title" content="@yield('og_title', $settings->name ?: $entreprise->name)">
    <meta name="twitter:description" content="@yield('og_description', $settings->meta_description ?: $settings->description)">

    <link rel="icon" href="{{ $settings->favicon_url ?? $entreprise->logo_url ?? asset('images/logo.jpeg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Couleurs choisies par le commerçant (Admin > Boutique en ligne >
         Apparence), injectées en variables CSS consommées par les classes
         Tailwind arbitraires ci-dessous (bg-[--store-primary], etc.). --}}
    <style>
        :root {
            --store-primary: {{ $settings->primary_color ?: \App\Models\OnlineStoreSettings::DEFAULT_PRIMARY_COLOR }};
            --store-secondary: {{ $settings->secondary_color ?: \App\Models\OnlineStoreSettings::DEFAULT_SECONDARY_COLOR }};
            --store-navbar: {{ $settings->navbar_color ?: \App\Models\OnlineStoreSettings::DEFAULT_NAVBAR_COLOR }};
            --store-button: {{ $settings->button_color ?: \App\Models\OnlineStoreSettings::DEFAULT_BUTTON_COLOR }};
            --store-link: {{ $settings->link_color ?: \App\Models\OnlineStoreSettings::DEFAULT_LINK_COLOR }};
            --store-footer: {{ $settings->footer_color ?: \App\Models\OnlineStoreSettings::DEFAULT_FOOTER_COLOR }};
        }
        a.store-link { color: var(--store-link); }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white text-slate-900 antialiased">

    @include('storefront.partials.nav')

    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-green-50 text-green-800 text-sm px-4 py-3">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('storefront.partials.footer')

    @stack('scripts')
</body>
</html>
