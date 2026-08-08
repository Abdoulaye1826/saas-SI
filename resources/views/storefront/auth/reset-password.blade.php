@extends('layouts.storefront')

@section('title', 'Réinitialiser le mot de passe — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">Nouveau mot de passe</h1>

    @if($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('store.account.password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Nouveau mot de passe</label>
            <input type="password" name="password" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" required class="w-full rounded-lg border-slate-200 text-sm">
        </div>

        <button type="submit" class="w-full rounded-lg py-3 font-semibold text-white" style="background: var(--store-button);">
            Réinitialiser le mot de passe
        </button>
    </form>
</div>
@endsection
