@extends('layouts.storefront')

@section('title', 'Créer un compte — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Créer un compte</h1>
    <p class="text-sm text-slate-500 mb-6">Suivez vos commandes et retrouvez votre historique d'achat.</p>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('store.account.register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Nom complet</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+221 XX XXX XX XX" class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Email (optionnel)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-200 text-sm">
            <p class="text-xs text-slate-400 mt-1">Nécessaire uniquement pour réinitialiser votre mot de passe.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Mot de passe</label>
            <input type="password" name="password" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>

        <button type="submit" class="w-full rounded-lg py-3 font-semibold text-white" style="background: var(--store-button);">
            Créer mon compte
        </button>
    </form>

    <p class="text-sm text-slate-500 text-center mt-6">
        Déjà un compte ? <a href="{{ route('store.account.login') }}" class="store-link font-medium">Se connecter</a>
    </p>
</div>
@endsection
