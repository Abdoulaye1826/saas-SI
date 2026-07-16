<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée — {{ $entreprise->name }} SI</title>
    @include('layouts.partials.favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
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
            <div class="auth-form-panel auth-form-panel--center">
                <div class="auth-mobile-brand" style="justify-content:center;">
                    <div class="auth-mobile-brand__icon">
                        <img src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="{{ $entreprise->name }}">
                    </div>
                    <div class="auth-mobile-brand__name">{{ $entreprise->name }}</div>
                </div>

                <h4 class="fw-bold mb-4">Session expirée</h4>

                <div class="alert alert-warning d-flex align-items-center gap-2 text-start" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Votre session a expiré. Veuillez vous reconnecter.</span>
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2 fw-medium">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Retour à la connexion
                </a>
            </div>
        </div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = @json(route('login'));
        }, 1500);
    </script>
</body>
</html>
