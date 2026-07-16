@extends('layouts.dashboard')

@section('title', 'Nouvelle avance client')
@section('page-title', 'Nouvelle avance client')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-person-check me-2"></i>Nouvelle avance client</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.client-advances.index') }}">Avances clients</a></li>
      <li class="breadcrumb-item active">Nouvelle</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.client-advances.store') }}">
      @csrf
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Client <span class="text-danger">*</span></label>
          <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
            <option value="">— Sélectionner —</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }}</option>
            @endforeach
          </select>
          @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        <div class="col-md-4 mb-3">
          <label class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
          @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Date <span class="text-danger">*</span></label>
          <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->toDateString()) }}" required>
          @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Mode de paiement <span class="text-danger">*</span></label>
          <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
            <option value="wave">Wave</option>
            <option value="orange_money">Orange Money</option>
            <option value="cash">Espèces</option>
          </select>
          @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Référence</label>
        <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference') }}">
        @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </form>
  </div>
</div>
@endsection
