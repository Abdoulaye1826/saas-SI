<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boutique indisponible — {{ $entreprise->name }}</title>
    <link rel="icon" href="{{ $settings->favicon_url ?? $entreprise->logo_url ?? asset('images/logo.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-50 text-slate-900 px-4">
    <div class="text-center max-w-md">
        @if($entreprise->logo_url)
            <img src="{{ $entreprise->logo_url }}" alt="{{ $entreprise->name }}" class="h-16 w-16 rounded-lg object-cover mx-auto mb-6">
        @endif
        <h1 class="text-2xl font-bold mb-2">Notre boutique est temporairement indisponible.</h1>
        <p class="text-slate-500">Merci de votre patience, nous serons bientôt de retour.</p>
        @if($entreprise->whatsapp_number)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $entreprise->whatsapp_number) }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 mt-6 px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium">
                Nous contacter sur WhatsApp
            </a>
        @endif
    </div>
</body>
</html>
