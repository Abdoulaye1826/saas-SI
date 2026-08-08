@extends('layouts.storefront')

@section('title', 'Mot de passe oublié — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Mot de passe oublié</h1>
    <p class="text-sm text-slate-500 mb-6">Indiquez l'email associé à votre compte pour recevoir un lien de réinitialisation.</p>

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

    <form method="POST" action="{{ route('store.account.password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border-slate-200 text-sm">
        </div>

        <button type="submit" class="w-full rounded-lg py-3 font-semibold text-white" style="background: var(--store-button);">
            Envoyer le lien
        </button>
    </form>

    <p class="text-sm text-slate-500 text-center mt-6">
        <a href="{{ route('store.account.login') }}" class="store-link font-medium">Retour à la connexion</a>
    </p>
</div>
@endsection
