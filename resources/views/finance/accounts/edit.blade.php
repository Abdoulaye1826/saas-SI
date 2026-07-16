@extends('layouts.dashboard')

@section('title', 'Modifier le compte')
@section('page-title', 'Modifier le compte')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-pencil me-2"></i>Modifier : {{ $account->name }}</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.accounts.index') }}">Comptes financiers</a></li>
      <li class="breadcrumb-item active">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.accounts.update', $account) }}">
      @csrf @method('PUT')
      @include('finance.accounts._form')
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
