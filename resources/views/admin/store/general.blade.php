@extends('layouts.dashboard')

@section('title', 'Boutique en ligne — Général')
@section('page-title', 'Boutique en ligne')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-shop me-2"></i>Boutique en ligne</h1>
  <p class="text-muted small mb-0">Activez et configurez les informations générales de votre boutique publique.</p>
</div>

@include('admin.store._nav')

<div class="row g-3 u-animate">
  <div class="col-lg-8">
    <form method="POST" action="{{ route('admin.store.general.update') }}" enctype="multipart/form-data" data-ui-form novalidate>
      @csrf @method('PUT')

      <div class="form-card mb-3">
        <div class="form-card__header">
          <h2><i class="bi bi-sliders"></i>Réglages généraux</h2>
          <p class="form-card__subtitle">Ces informations apparaissent sur votre boutique publique.</p>
        </div>

        <div class="form-card__body" data-form-sections>

          {{-- ── Section : Statut ─────────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-statut">
              <span class="form-section__title"><i class="bi bi-power"></i>Statut de la boutique</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-statut">
              <div class="field-group mb-0">
                @foreach(\App\Enums\StoreStatus::cases() as $status)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="status" id="status_{{ $status->value }}"
                           value="{{ $status->value }}"
                           {{ old('status', $settings->status?->value ?? 'disabled') === $status->value ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_{{ $status->value }}">
                      {{ $status->label() }}
                      @if($status !== \App\Enums\StoreStatus::Active)
                        <span class="text-muted small d-block">Les visiteurs verront « Notre boutique est temporairement indisponible. »</span>
                      @endif
                    </label>
                  </div>
                @endforeach
                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>

          {{-- ── Section : Logo & favicon ────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-identite">
              <span class="form-section__title"><i class="bi bi-image"></i>Logo &amp; favicon</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-identite">
              <div class="row align-items-start">
                <div class="col-md-6 field-group mb-md-0">
                  <label for="logo" class="form-label">Logo</label>
                  <label class="image-dropzone" for="logo" tabindex="0">
                    <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="image-dropzone__icon"><i class="bi bi-cloud-arrow-up"></i></div>
                    <div class="image-dropzone__text"><strong>Cliquez</strong> ou glissez-déposez une image ici<br>JPG, PNG ou WEBP</div>
                  </label>
                  @error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                  <div class="image-preview" style="{{ $settings->logo_url ? 'display:flex' : 'display:none' }}">
                    @if($settings->logo_url)
                      <img src="{{ $settings->logo_url }}" alt="Logo boutique" loading="lazy">
                      <button type="button" class="image-preview__remove" data-remove-target="remove_logo">
                        <i class="bi bi-trash me-1"></i>Supprimer le logo
                      </button>
                      <input type="checkbox" id="remove_logo" name="remove_logo" value="1" class="d-none">
                    @endif
                  </div>
                </div>
                <div class="col-md-6 field-group mb-0">
                  <label for="favicon" class="form-label">Favicon</label>
                  <label class="image-dropzone" for="favicon" tabindex="0">
                    <input type="file" id="favicon" name="favicon" accept="image/jpeg,image/png,image/jpg,image/webp,image/x-icon">
                    <div class="image-dropzone__icon"><i class="bi bi-cloud-arrow-up"></i></div>
                    <div class="image-dropzone__text"><strong>Cliquez</strong> ou glissez-déposez une image ici<br>JPG, PNG, WEBP ou ICO</div>
                  </label>
                  @error('favicon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                  <div class="image-preview" style="{{ $settings->favicon_url ? 'display:flex' : 'display:none' }}">
                    @if($settings->favicon_url)
                      <img src="{{ $settings->favicon_url }}" alt="Favicon boutique" loading="lazy" style="max-width:48px;">
                      <button type="button" class="image-preview__remove" data-remove-target="remove_favicon">
                        <i class="bi bi-trash me-1"></i>Supprimer le favicon
                      </button>
                      <input type="checkbox" id="remove_favicon" name="remove_favicon" value="1" class="d-none">
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── Section : Informations boutique ─────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-infos">
              <span class="form-section__title"><i class="bi bi-info-circle"></i>Informations</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-infos">
              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="name" class="form-label">Nom de la boutique</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $settings->name) }}" placeholder="{{ $entreprise->name }}">
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="slogan" class="form-label">Slogan</label>
                  <input type="text" class="form-control @error('slogan') is-invalid @enderror" id="slogan" name="slogan" value="{{ old('slogan', $settings->slogan) }}" placeholder="Ex : Les meilleures consoles au meilleur prix">
                  @error('slogan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="field-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $settings->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="phone" class="form-label">Téléphone</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-telephone field-icon"></i>
                    <input type="text" class="form-control has-icon @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $settings->phone) }}" placeholder="+221 XX XXX XX XX">
                  </div>
                  @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="whatsapp_number" class="form-label">WhatsApp</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-whatsapp field-icon"></i>
                    <input type="text" class="form-control has-icon @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="221781928588">
                  </div>
                  @error('whatsapp_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="form-text">Utilisé par le bouton "Commander sur WhatsApp".</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="email" class="form-label">Email</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-envelope field-icon"></i>
                    <input type="email" class="form-control has-icon @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $settings->email) }}">
                  </div>
                  @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="address" class="form-label">Adresse</label>
                  <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $settings->address) }}">
                  @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="field-group mb-0">
                <label for="opening_hours" class="form-label">Horaires d'ouverture</label>
                <textarea class="form-control @error('opening_hours') is-invalid @enderror" id="opening_hours" name="opening_hours" rows="3" placeholder="Lundi - Vendredi : 9h - 19h&#10;Samedi : 9h - 14h">{{ old('opening_hours', is_array($settings->opening_hours) ? implode("\n", $settings->opening_hours) : $settings->opening_hours) }}</textarea>
                @error('opening_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Une ligne par plage horaire.</div>
              </div>
            </div>
          </div>

          {{-- ── Section : Fonctionnalités ────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-fonctionnalites">
              <span class="form-section__title"><i class="bi bi-toggles"></i>Fonctionnalités</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-fonctionnalites">
              <div class="field-group mb-0">
                <div class="form-check form-switch fs-6 ps-1">
                  <input class="form-check-input" type="checkbox" id="reviews_enabled" name="reviews_enabled" value="1" role="switch"
                         {{ old('reviews_enabled', $settings->reviews_enabled) ? 'checked' : '' }}>
                  <label class="form-check-label" for="reviews_enabled">Avis clients activés</label>
                </div>
                <div class="form-text">Permet aux clients connectés de noter et commenter les produits. Les avis déjà validés restent affichés même si vous désactivez cette option.</div>
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

  <div class="col-lg-4">
    <div class="detail-card">
      <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-lightbulb text-muted"></i>
        <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:.05em;">À savoir</span>
      </div>
      <p class="text-muted small mb-2">Tant que le statut n'est pas « Boutique active », les visiteurs voient une page d'indisponibilité — la boutique n'est jamais accessible par erreur.</p>
      <p class="text-muted small mb-0">Les couleurs et la bannière d'accueil se règlent dans l'onglet « Apparence ».</p>
    </div>
  </div>
</div>
@endsection
