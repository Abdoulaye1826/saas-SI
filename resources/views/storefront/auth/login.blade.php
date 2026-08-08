@extends('layouts.storefront')

@section('title', 'Connexion — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Connexion</h1>

    @if(session('status'))
        <div class="mb-6 rounded-lg bg-green-50 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('store.account.login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required autofocus class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Mot de passe</label>
            <input type="password" name="password" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-500">
            <input type="checkbox" name="remember">
            Se souvenir de moi
        </label>

        <button type="submit" class="w-full rounded-lg py-3 font-semibold text-white" style="background: var(--store-button);">
            Se connecter
        </button>
    </form>

    <div class="flex justify-between text-sm mt-6">
        <a href="{{ route('store.account.register') }}" class="store-link font-medium">Créer un compte</a>
        <a href="{{ route('store.account.password.request') }}" class="store-link">Mot de passe oublié ?</a>
    </div>
</div>
@endsection
