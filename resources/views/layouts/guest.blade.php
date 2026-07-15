<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion') — {{ $entreprise->name }} SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}?v={{ @filemtime(public_path('css/dashboard.css')) ?: '1' }}" rel="stylesheet">
    @include('layouts.partials.brand-theme')
</head>
<body>
    <div class="auth-shell">
        <div class="auth-shell__brand">
            <div class="auth-brand__content">
                <div class="auth-brand__logo">
                    <img src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="{{ $entreprise->name }}">
                </div>
                <div class="auth-brand__name">{{ $entreprise->name }}</div>
                <p class="auth-brand__tagline">Gérez vos ventes, votre stock et votre facturation depuis un seul endroit.</p>
            </div>
            <div class="auth-brand__footer">&copy; {{ date('Y') }} {{ $entreprise->name }}</div>
        </div>

        <div class="auth-shell__form">
            <div class="auth-form-panel">
                <div class="auth-mobile-brand">
                    <div class="auth-mobile-brand__icon">
                        <img src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="{{ $entreprise->name }}">
                    </div>
                    <div class="auth-mobile-brand__name">{{ $entreprise->name }}</div>
                </div>

                <h4 class="fw-bold mb-1">@yield('title', 'Connexion')</h4>
                <p class="text-muted small mb-4">Système d'information</p>

                @if (session('status'))
                    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
