<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée — Mboup Gaming SI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card text-center">
            <div class="auth-logo">
                <div class="logo-icon">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Mboup Gaming">
                </div>
                <h4 class="fw-bold mb-0">Mboup Gaming</h4>
                <small class="text-muted">Système d'information</small>
            </div>
            <div class="alert alert-warning d-flex align-items-center gap-2 text-start" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Votre session a expiré. Veuillez vous reconnecter.</span>
            </div>
            <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2 fw-medium">
                <i class="bi bi-box-arrow-in-right me-2"></i>Retour à la connexion
            </a>
        </div>
    </div>
    <script>
        setTimeout(function () {
            window.location.href = @json(route('login'));
        }, 1500);
    </script>
</body>
</html>
