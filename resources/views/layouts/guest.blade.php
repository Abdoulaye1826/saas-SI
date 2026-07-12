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
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <div class="logo-icon">
                    <img src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="{{ $entreprise->name }}">
                </div>
                <h4 class="fw-bold mb-0">{{ $entreprise->name }}</h4>
                <small class="text-muted">Système d'information</small>
            </div>
            @if (session('status'))
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
