@extends('layouts.dashboard')

@section('title', 'Boutique en ligne — Apparence')
@section('page-title', 'Boutique en ligne')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-shop me-2"></i>Boutique en ligne</h1>
  <p class="text-muted small mb-0">Personnalisez les couleurs et la bannière principale de votre boutique, sans écrire de code.</p>
</div>

@include('admin.store._nav')

<div class="row g-3 u-animate">
  <div class="col-lg-8">
    <form method="POST" action="{{ route('admin.store.appearance.update') }}" enctype="multipart/form-data" data-ui-form novalidate>
      @csrf @method('PUT')

      <div class="form-card mb-3">
        <div class="form-card__header">
          <h2><i class="bi bi-palette"></i>Apparence</h2>
          <p class="form-card__subtitle">Aperçu en temps réel dans la colonne de droite.</p>
        </div>

        <div class="form-card__body" data-form-sections>

          {{-- ── Section : Couleurs ──────────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-couleurs">
              <span class="form-section__title"><i class="bi bi-eyedropper"></i>Couleurs</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-couleurs">
              @php
                $colorFields = [
                  'primary_color' => ['Couleur principale', \App\Models\OnlineStoreSettings::DEFAULT_PRIMARY_COLOR],
                  'secondary_color' => ['Couleur secondaire', \App\Models\OnlineStoreSettings::DEFAULT_SECONDARY_COLOR],
                  'navbar_color' => ['Couleur navbar', \App\Models\OnlineStoreSettings::DEFAULT_NAVBAR_COLOR],
                  'button_color' => ['Couleur des boutons', \App\Models\OnlineStoreSettings::DEFAULT_BUTTON_COLOR],
                  'link_color' => ['Couleur des liens', \App\Models\OnlineStoreSettings::DEFAULT_LINK_COLOR],
                  'footer_color' => ['Couleur du footer', \App\Models\OnlineStoreSettings::DEFAULT_FOOTER_COLOR],
                ];
              @endphp
              <div class="row">
                @foreach($colorFields as $field => [$label, $default])
                  <div class="col-md-4 field-group">
                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                    <div class="d-flex align-items-center gap-2">
                      <input type="color" class="form-control form-control-color @error($field) is-invalid @enderror"
                             id="{{ $field }}" name="{{ $field }}"
                             value="{{ old($field, $settings->{$field} ?? $default) }}"
                             data-color-preview>
                      <code class="small text-muted" data-color-value>{{ old($field, $settings->{$field} ?? $default) }}</code>
                    </div>
                    @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          {{-- ── Section : Bannière principale ───────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-banniere">
              <span class="form-section__title"><i class="bi bi-image"></i>Bannière principale</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-banniere">
              <div class="field-group">
                <label for="hero_image" class="form-label">Image de bannière</label>
                <label class="image-dropzone" for="hero_image" tabindex="0">
                  <input type="file" id="hero_image" name="hero_image" accept="image/jpeg,image/png,image/jpg,image/webp">
                  <div class="image-dropzone__icon"><i class="bi bi-cloud-arrow-up"></i></div>
                  <div class="image-dropzone__text"><strong>Cliquez</strong> ou glissez-déposez une image ici<br>JPG, PNG ou WEBP — format large recommandé</div>
                </label>
                @error('hero_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                <div class="image-preview" style="{{ $settings->hero_image_url ? 'display:flex' : 'display:none' }}">
                  @if($settings->hero_image_url)
                    <img src="{{ $settings->hero_image_url }}" alt="Bannière" loading="lazy">
                    <button type="button" class="image-preview__remove" data-remove-target="remove_hero_image">
                      <i class="bi bi-trash me-1"></i>Supprimer la bannière
                    </button>
                    <input type="checkbox" id="remove_hero_image" name="remove_hero_image" value="1" class="d-none">
                  @endif
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="hero_title" class="form-label">Titre</label>
                  <input type="text" class="form-control @error('hero_title') is-invalid @enderror" id="hero_title" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}" placeholder="Les meilleures consoles et accessoires au meilleur prix">
                  @error('hero_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="hero_subtitle" class="form-label">Sous-titre</label>
                  <input type="text" class="form-control @error('hero_subtitle') is-invalid @enderror" id="hero_subtitle" name="hero_subtitle" value="{{ old('hero_subtitle', $settings->hero_subtitle) }}">
                  @error('hero_subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="hero_button_label" class="form-label">Texte du bouton</label>
                  <input type="text" class="form-control @error('hero_button_label') is-invalid @enderror" id="hero_button_label" name="hero_button_label" value="{{ old('hero_button_label', $settings->hero_button_label) }}" placeholder="Découvrir nos produits">
                  @error('hero_button_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group mb-0">
                  <label for="hero_button_url" class="form-label">Lien du bouton</label>
                  <input type="text" class="form-control @error('hero_button_url') is-invalid @enderror" id="hero_button_url" name="hero_button_url" value="{{ old('hero_button_url', $settings->hero_button_url) }}" placeholder="/boutique/produits">
                  @error('hero_button_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
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
    <div class="detail-card" style="position:sticky; top:1rem;">
      <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-eye text-muted"></i>
        <span class="fw-semibold small text-muted text-uppercase" style="letter-spacing:.05em;">Aperçu en direct</span>
      </div>

      <div id="storePreviewNavbar" class="rounded-3 p-3 mb-2 d-flex align-items-center justify-content-between">
        <span class="fw-bold text-white" id="storePreviewName">{{ $settings->name ?: $entreprise->name }}</span>
        <i class="bi bi-cart3 text-white"></i>
      </div>

      <div id="storePreviewHero" class="rounded-3 p-4 mb-2 text-white">
        <div class="fw-bold" id="storePreviewHeroTitle">{{ $settings->hero_title ?: 'Titre de la bannière' }}</div>
        <div class="small mb-2" id="storePreviewHeroSubtitle">{{ $settings->hero_subtitle ?: 'Sous-titre de la bannière' }}</div>
        <button type="button" class="btn btn-sm" id="storePreviewButton" style="color:#fff;">
          {{ $settings->hero_button_label ?: 'Découvrir nos produits' }}
        </button>
      </div>

      <div id="storePreviewFooter" class="rounded-3 p-3 text-white small text-center">
        Footer de la boutique
      </div>

      <p class="text-muted small mb-0 mt-3">Ce mini-aperçu montre où vos couleurs apparaissent sur la boutique publique.</p>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const navbarInput = document.getElementById('navbar_color');
    const buttonInput = document.getElementById('button_color');
    const primaryInput = document.getElementById('primary_color');
    const footerInput = document.getElementById('footer_color');
    const heroTitleInput = document.getElementById('hero_title');
    const heroSubtitleInput = document.getElementById('hero_subtitle');
    const heroButtonInput = document.getElementById('hero_button_label');

    const previewNavbar = document.getElementById('storePreviewNavbar');
    const previewHero = document.getElementById('storePreviewHero');
    const previewButton = document.getElementById('storePreviewButton');
    const previewFooter = document.getElementById('storePreviewFooter');
    const previewHeroTitle = document.getElementById('storePreviewHeroTitle');
    const previewHeroSubtitle = document.getElementById('storePreviewHeroSubtitle');

    function updatePreview() {
      if (previewNavbar) previewNavbar.style.background = navbarInput.value;
      if (previewButton) previewButton.style.background = buttonInput.value;
      if (previewHero) previewHero.style.background = primaryInput.value;
      if (previewFooter) previewFooter.style.background = footerInput.value;
    }

    document.querySelectorAll('[data-color-preview]').forEach((input) => {
      input.addEventListener('input', function () {
        const valueEl = input.parentElement.querySelector('[data-color-value]');
        if (valueEl) valueEl.textContent = input.value;
        updatePreview();
      });
    });

    heroTitleInput?.addEventListener('input', () => {
      previewHeroTitle.textContent = heroTitleInput.value || 'Titre de la bannière';
    });
    heroSubtitleInput?.addEventListener('input', () => {
      previewHeroSubtitle.textContent = heroSubtitleInput.value || 'Sous-titre de la bannière';
    });
    heroButtonInput?.addEventListener('input', () => {
      previewButton.textContent = heroButtonInput.value || 'Découvrir nos produits';
    });

    updatePreview();
  });
</script>
@endpush
