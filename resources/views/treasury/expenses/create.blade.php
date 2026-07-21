@extends('layouts.dashboard')

@section('title', 'Nouvelle dépense')
@section('page-title', 'Nouvelle dépense')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-dash-circle me-2"></i>Nouvelle dépense</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('treasury.dashboard') }}">Trésorerie</a></li>
      <li class="breadcrumb-item active">Nouvelle dépense</li>
    </ol>
  </nav>
</div>

<div class="form-shell u-animate">
  <form method="POST" action="{{ route('treasury.expenses.store') }}" novalidate>
    @csrf
    <div class="form-card">
      <div class="form-card__header">
        <h2><i class="bi bi-dash-circle"></i>Détails de la dépense</h2>
        <p class="form-card__subtitle">Les champs marqués <span class="req">*</span> sont obligatoires.</p>
      </div>
      <div class="form-card__body">
        <div class="row">
          <div class="col-md-6 field-group">
            <label for="date" class="form-label">Date <span class="req">*</span></label>
            <input type="date" class="form-control @error('date') is-invalid @enderror"
                   id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 field-group">
            <label for="amount" class="form-label">Montant (FCFA) <span class="req">*</span></label>
            <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror"
                   id="amount" name="amount" value="{{ old('amount') }}" placeholder="Ex : 15000" required>
            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="field-group">
          <label for="category" class="form-label">Catégorie <span class="req">*</span></label>
          <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
            <option value="">Choisir une catégorie...</option>
            @foreach(\App\Enums\TreasuryExpenseCategory::cases() as $cat)
              <option value="{{ $cat->value }}" @selected(old('category') === $cat->value)>{{ $cat->label() }}</option>
            @endforeach
          </select>
          @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
          <div class="col-md-6 field-group">
            <label for="supplier_name" class="form-label">Nom du fournisseur</label>
            <input type="text" class="form-control @error('supplier_name') is-invalid @enderror"
                   id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}"
                   placeholder="Ex : Fournisseur Dakar Electronics">
            @error('supplier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6 field-group">
            <label for="product_reference" class="form-label">Référence produit</label>
            <input type="text" class="form-control @error('product_reference') is-invalid @enderror"
                   id="product_reference" name="product_reference" value="{{ old('product_reference') }}"
                   placeholder="Scanner ou saisir la référence" autocomplete="off">
            @error('product_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="field-group mb-0">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description" name="description" rows="3"
                    placeholder="Précisions sur cette dépense...">{{ old('description') }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="form-card__footer">
        <a href="{{ route('treasury.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i>Annuler</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer la dépense</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
// Une douchette code-barres/QR envoie le code puis "Entrée" : sans ce
// garde-fou, ça soumettrait prématurément le formulaire dès le scan de la
// référence produit.
document.getElementById('product_reference')?.addEventListener('keydown', function (e) {
  if (e.key === 'Enter') e.preventDefault();
});
</script>
@endpush
