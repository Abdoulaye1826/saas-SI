@extends('layouts.dashboard')

@php $type = $transaction->type->value; @endphp

@section('title', 'Modifier une écriture')
@section('page-title', 'Modifier une écriture')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-pencil me-2"></i>Modifier l'écriture</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.transactions.index', ['type' => $type]) }}">{{ $type === 'out' ? "Sorties d'argent" : "Entrées d'argent" }}</a></li>
      <li class="breadcrumb-item active">Modifier</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.transactions.update', $transaction) }}" enctype="multipart/form-data">
      @csrf @method('PUT')
      @include('finance.transactions._form')
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
