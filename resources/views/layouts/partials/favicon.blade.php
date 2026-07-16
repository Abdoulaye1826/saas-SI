{{-- Icône de l'onglet, icône "Ajouter à l'écran d'accueil" (iOS/Android) et
     manifeste web : reflètent le logo et la couleur de marque du client
     plutôt qu'une icône générique. --}}
<link rel="icon" href="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}">
<link rel="apple-touch-icon" href="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}">
<link rel="manifest" href="{{ route('manifest') }}">
<meta name="theme-color" content="{{ $entreprise->accent_color ?: \App\Models\Entreprise::DEFAULT_ACCENT_COLOR }}">
