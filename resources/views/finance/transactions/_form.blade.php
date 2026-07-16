<input type="hidden" name="type" value="{{ $type }}">

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Compte concerné <span class="text-danger">*</span></label>
    <select name="financial_account_id" class="form-select @error('financial_account_id') is-invalid @enderror" required>
      <option value="">— Sélectionner —</option>
      @foreach($accounts as $account)
        <option value="{{ $account->id }}" @selected(old('financial_account_id', $transaction->financial_account_id ?? '') == $account->id)>{{ $account->name }}</option>
      @endforeach
    </select>
    @error('financial_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Catégorie <span class="text-danger">*</span></label>
    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
      <option value="">— Sélectionner —</option>
      @foreach($categories as $category)
        <option value="{{ $category->value }}" @selected(old('category', $transaction->category->value ?? '') === $category->value)>{{ $category->label() }}</option>
      @endforeach
    </select>
    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
    <input type="number" step="0.01" min="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror"
           value="{{ old('amount', $transaction->amount ?? '') }}" required>
    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Date <span class="text-danger">*</span></label>
    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
           value="{{ old('date', isset($transaction) ? $transaction->date->toDateString() : now()->toDateString()) }}" required>
    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

@if($type === 'out')
  <div class="mb-3">
    <label class="form-label">Fournisseur</label>
    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
      <option value="">— Aucun —</option>
      @foreach(\App\Models\Supplier::orderBy('name')->get() as $supplier)
        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $transaction->supplier_id ?? '') == $supplier->id)>{{ $supplier->name }}</option>
      @endforeach
    </select>
    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
@else
  <div class="mb-3">
    <label class="form-label">Client</label>
    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
      <option value="">— Aucun —</option>
      @foreach(\App\Models\Customer::orderBy('full_name')->get() as $customer)
        <option value="{{ $customer->id }}" @selected(old('customer_id', $transaction->customer_id ?? '') == $customer->id)>{{ $customer->full_name }}</option>
      @endforeach
    </select>
    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
@endif

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Référence</label>
    <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $transaction->reference ?? '') }}">
    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Justificatif (PDF, image, facture, reçu)</label>
    <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.webp">
    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if(isset($transaction) && $transaction->attachment_path)
      <div class="form-text">
        Justificatif actuel : <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($transaction->attachment_path) }}" target="_blank">voir le fichier</a>
      </div>
    @endif
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $transaction->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
