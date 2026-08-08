@extends('layouts.dashboard')

@section('title', 'Boutique en ligne — Livraison')
@section('page-title', 'Boutique en ligne')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-shop me-2"></i>Boutique en ligne</h1>
  <p class="text-muted small mb-0">Configurez les modes et frais de livraison proposés aux clients.</p>
</div>

@include('admin.store._nav')

<div class="row g-3 u-animate">
  <div class="col-lg-8">
    <form method="POST" action="{{ route('admin.store.delivery.update') }}" data-ui-form novalidate>
      @csrf @method('PUT')

      <div class="form-card mb-3">
        <div class="form-card__header">
          <h2><i class="bi bi-truck"></i>Livraison</h2>
          <p class="form-card__subtitle">Ces réglages déterminent les frais affichés au client au moment de la commande.</p>
        </div>

        <div class="form-card__body" data-form-sections>
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-modes">
              <span class="form-section__title"><i class="bi bi-toggles"></i>Modes de livraison</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-modes">
              <div class="row">
                <div class="col-md-6 field-group">
                  <div class="form-check form-switch fs-6 ps-1">
                    <input class="form-check-input" type="checkbox" id="delivery_enabled" name="delivery_enabled" value="1" role="switch"
                           {{ old('delivery_enabled', $settings->delivery_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="delivery_enabled">Livraison à domicile</label>
                  </div>
                </div>
                <div class="col-md-6 field-group mb-0">
                  <div class="form-check form-switch fs-6 ps-1">
                    <input class="form-check-input" type="checkbox" id="pickup_enabled" name="pickup_enabled" value="1" role="switch"
                           {{ old('pickup_enabled', $settings->pickup_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="pickup_enabled">Retrait en boutique</label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-frais">
              <span class="form-section__title"><i class="bi bi-cash-coin"></i>Frais de livraison</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-frais">
              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="delivery_fee_dakar" class="form-label">Dakar</label>
                  <input type="number" step="1" min="0" class="form-control @error('delivery_fee_dakar') is-invalid @enderror"
                         id="delivery_fee_dakar" name="delivery_fee_dakar" value="{{ old('delivery_fee_dakar', $settings->delivery_fee_dakar) }}">
                  @error('delivery_fee_dakar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <div class="form-text">FCFA</div>
                </div>
                <div class="col-md-6 field-group">
                  <label for="delivery_fee_other" class="form-label">Hors Dakar</label>
                  <input type="number" step="1" min="0" class="form-control @error('delivery_fee_other') is-invalid @enderror"
                         id="delivery_fee_other" name="delivery_fee_other" value="{{ old('delivery_fee_other', $settings->delivery_fee_other) }}">
                  @error('delivery_fee_other')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <div class="form-text">FCFA</div>
                </div>
              </div>
              <div class="field-group mb-0">
                <label for="free_delivery_threshold" class="form-label">Livraison gratuite à partir de</label>
                <input type="number" step="1" min="0" class="form-control @error('free_delivery_threshold') is-invalid @enderror"
                       id="free_delivery_threshold" name="free_delivery_threshold" value="{{ old('free_delivery_threshold', $settings->free_delivery_threshold) }}"
                       placeholder="Laisser vide pour désactiver">
                @error('free_delivery_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">FCFA — montant du panier au-delà duquel la livraison est offerte.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-card__footer">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
