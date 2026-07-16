@extends('layouts.dashboard')

@section('title', $type === 'out' ? "Nouvelle sortie d'argent" : "Nouvelle entrée d'argent")
@section('page-title', $type === 'out' ? "Nouvelle sortie d'argent" : "Nouvelle entrée d'argent")

@section('content')
<div class="page-header">
  <h1><i class="bi bi-{{ $type === 'out' ? 'arrow-up-circle' : 'arrow-down-circle' }} me-2"></i>{{ $type === 'out' ? "Nouvelle sortie d'argent" : "Nouvelle entrée d'argent" }}</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.transactions.index', ['type' => $type]) }}">{{ $type === 'out' ? "Sorties d'argent" : "Entrées d'argent" }}</a></li>
      <li class="breadcrumb-item active">Nouvelle écriture</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.transactions.store') }}" enctype="multipart/form-data">
      @csrf
      @include('finance.transactions._form')
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
