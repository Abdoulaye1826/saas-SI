@extends('layouts.dashboard')

@section('title', 'Nouveau virement interne')
@section('page-title', 'Nouveau virement interne')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-arrow-left-right me-2"></i>Nouveau virement interne</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item"><a href="{{ route('finance.transfers.index') }}">Virements internes</a></li>
      <li class="breadcrumb-item active">Nouveau</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('finance.transfers.store') }}">
      @csrf
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Compte source <span class="text-danger">*</span></label>
          <select name="from_account_id" class="form-select @error('from_account_id') is-invalid @enderror" required>
            <option value="">— Sélectionner —</option>
            @foreach($accounts as $account)
              <option value="{{ $account->id }}" @selected(old('from_account_id') == $account->id)>{{ $account->name }} ({{ number_format((float) $account->current_balance, 0, ',', ' ') }} FCFA)</option>
            @endforeach
          </select>
          @error('from_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Compte destination <span class="text-danger">*</span></label>
          <select name="to_account_id" class="form-select @error('to_account_id') is-invalid @enderror" required>
            <option value="">— Sélectionner —</option>
            @foreach($accounts as $account)
              <option value="{{ $account->id }}" @selected(old('to_account_id') == $account->id)>{{ $account->name }}</option>
            @endforeach
          </select>
          @error('to_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

      <div class="mb-3">
        <label class="form-label">Motif</label>
        <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" value="{{ old('reason') }}">
        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Effectuer le virement</button>
    </form>
  </div>
</div>
@endsection
