@extends('layouts.dashboard')

@section('title', 'Nouvelle avance fournisseur')
@section('page-title', 'Nouvelle avance fournisseur')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-truck me-2"></i>Nouvelle avance fournisseur</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.supplier-advances.index') }}">Avances fournisseurs</a></li>
      <li class="breadcrumb-item active">Nouvelle</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.supplier-advances.store') }}">
      @csrf
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Fournisseur <span class="text-danger">*</span></label>
          <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">— Sélectionner —</option>
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
          </select>
          @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Compte concerné <span class="text-danger">*</span></label>
          <select name="financial_account_id" class="form-select @error('financial_account_id') is-invalid @enderror" required>
            <option value="">— Sélectionner —</option>
            @foreach($accounts as $account)
              <option value="{{ $account->id }}" @selected(old('financial_account_id') == $account->id)>{{ $account->name }}</option>
            @endforeach
          </select>
          @error('financial_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
          @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Date <span class="text-danger">*</span></label>
          <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->toDateString()) }}" required>
          @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Référence</label>
          <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}">
          @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Observation</label>
          <input type="text" name="observation" class="form-control @error('observation') is-invalid @enderror" value="{{ old('observation') }}">
          @error('observation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
