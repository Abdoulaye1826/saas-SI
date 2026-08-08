@extends('layouts.storefront')

@section('title', 'Mon profil — '.($settings->name ?: $entreprise->name))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('storefront.account._nav')

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 text-green-800 text-sm px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="border border-slate-100 rounded-xl p-5 mb-6">
        <h2 class="font-semibold text-slate-800 mb-4">Mes informations</h2>

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 text-red-700 text-sm px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('store.account.profile.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Nom complet</label>
                <input type="text" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Ville</label>
                <input type="text" name="city" value="{{ old('city', $customer->city) }}" class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-600 mb-1">Adresse</label>
                <input type="text" name="address" value="{{ old('address', $customer->address) }}" class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-lg px-6 py-2.5 font-semibold text-white" style="background: var(--store-button);">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <div class="border border-slate-100 rounded-xl p-5">
        <h2 class="font-semibold text-slate-800 mb-4">Mot de passe</h2>
        <form method="POST" action="{{ route('store.account.password.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Mot de passe actuel</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div></div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Nouveau mot de passe</label>
                <input type="password" name="password" required class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Confirmer</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-slate-200 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="rounded-lg px-6 py-2.5 font-semibold text-white" style="background: var(--store-button);">
                    Changer le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
