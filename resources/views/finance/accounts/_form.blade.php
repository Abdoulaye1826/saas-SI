<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Nom du compte <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $account->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Type <span class="text-danger">*</span></label>
    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
      @foreach(\App\Enums\FinancialAccountType::cases() as $type)
        <option value="{{ $type->value }}" @selected(old('type', $account->type->value ?? '') === $type->value)>{{ $type->label() }}</option>
      @endforeach
    </select>
    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Mode de paiement associé</label>
    <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
      <option value="">— Aucun —</option>
      <option value="wave" @selected(old('payment_method', $account->payment_method->value ?? '') === 'wave')>Wave</option>
      <option value="orange_money" @selected(old('payment_method', $account->payment_method->value ?? '') === 'orange_money')>Orange Money</option>
      <option value="cash" @selected(old('payment_method', $account->payment_method->value ?? '') === 'cash')>Espèces</option>
    </select>
    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Les paiements enregistrés avec ce mode créditeront automatiquement ce compte.</div>
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Solde {{ isset($account) ? 'actuel' : 'initial' }}</label>
    <input type="number" step="0.01" name="current_balance" class="form-control @error('current_balance') is-invalid @enderror"
           value="{{ old('current_balance', $account->current_balance ?? 0) }}">
    @error('current_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror">{{ old('description', $account->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3 form-check form-switch">
  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $account->is_active ?? true))>
  <label class="form-check-label" for="is_active">Compte actif</label>
</div>
