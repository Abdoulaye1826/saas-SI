<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $entreprise->name }} SI</title>
    @include('layouts.partials.favicon')

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Version horodatée sur le fichier (filemtime) : force le navigateur à
         recharger le CSS dès qu'il change en production, au lieu de servir
         une version en cache après un déploiement. --}}
    <link href="{{ asset('css/dashboard.css') }}?v={{ @filemtime(public_path('css/dashboard.css')) ?: '1' }}" rel="stylesheet">
    <link href="{{ asset('css/forms-ui.css') }}?v={{ @filemtime(public_path('css/forms-ui.css')) ?: '1' }}" rel="stylesheet">
    @include('layouts.partials.brand-theme')

    @stack('styles')
</head>
<body>
    {{-- Overlay mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Contenu principal --}}
    <div class="main-wrapper">
        @include('layouts.partials.navbar')

        <main class="page-content">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/forms-ui.js') }}?v={{ @filemtime(public_path('js/forms-ui.js')) ?: '1' }}"></script>
    <script>
        window.APP_KEEP_ALIVE_URL = @json(route('keep-alive'));
        window.APP_LOGIN_URL = @json(route('login'));
    </script>
    <script src="{{ asset('js/session-keepalive.js') }}?v={{ @filemtime(public_path('js/session-keepalive.js')) ?: '1' }}"></script>
    <script>
        // Toggle sidebar mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        });
        document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
