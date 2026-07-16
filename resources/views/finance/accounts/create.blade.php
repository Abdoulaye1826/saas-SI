@extends('layouts.dashboard')

@section('title', 'Nouveau compte financier')
@section('page-title', 'Nouveau compte financier')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-bank me-2"></i>Nouveau compte financier</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.accounts.index') }}">Comptes financiers</a></li>
      <li class="breadcrumb-item active">Nouveau</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.accounts.store') }}">
      @csrf
      @include('finance.accounts._form')
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
