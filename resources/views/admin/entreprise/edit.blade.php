@extends('layouts.dashboard')

@section('title', "Informations de l'entreprise")
@section('page-title', "Informations de l'entreprise")

@section('content')
<div class="page-header">
  <h1><i class="bi bi-building me-2"></i>Informations de l'entreprise</h1>
  <p class="text-muted small mb-0">Ces informations apparaissent sur la connexion, le tableau de bord et vos factures/devis PDF — aucune donnée technique à modifier.</p>
</div>

<div class="row g-3 u-animate">
  <div class="col-lg-8">
    <form method="POST" action="{{ route('admin.entreprise.update') }}" enctype="multipart/form-data" data-ui-form novalidate>
      @csrf @method('PUT')

      <div class="form-card mb-3">
        <div class="form-card__header">
          <h2><i class="bi bi-building"></i>Fiche entreprise</h2>
          <p class="form-card__subtitle">Renseignez les informations qui identifient votre entreprise dans tout le système.</p>
        </div>

        <div class="form-card__body" data-form-sections>

          {{-- ── Section : Logo & identité ──────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-identite">
              <span class="form-section__title"><i class="bi bi-image"></i>Logo &amp; identité</span>
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

                  <div class="image-preview" style="{{ $entreprise->logo_url ? 'display:flex' : 'display:none' }}">
                    @if($entreprise->logo_url)
                      <img src="{{ $entreprise->logo_url }}" alt="{{ $entreprise->name }}" loading="lazy">
                    @endif
                  </div>
                  <div class="form-text">Utilisé sur la connexion, la sidebar et vos documents PDF. Une image carrée donne le meilleur rendu.</div>
                </div>
                <div class="col-md-6 field-group">
                  <label for="name" class="form-label">Nom commercial <span class="req">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $entreprise->name) }}" required>
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror

                  <label for="legal_name" class="form-label mt-3">Raison sociale</label>
                  <input type="text" class="form-control @error('legal_name') is-invalid @enderror" id="legal_name" name="legal_name" value="{{ old('legal_name', $entreprise->legal_name) }}">
                  @error('legal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <div class="form-text">Dénomination légale complète, si différente du nom commercial.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── Section : Coordonnées ──────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-coordonnees">
              <span class="form-section__title"><i class="bi bi-telephone"></i>Coordonnées</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-coordonnees">
              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="phone" class="form-label">Téléphone</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-telephone field-icon"></i>
                    <input type="text" class="form-control has-icon @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $entreprise->phone) }}" placeholder="+221 XX XXX XX XX">
                  </div>
                  @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="whatsapp_number" class="form-label">WhatsApp</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-whatsapp field-icon"></i>
                    <input type="text" class="form-control has-icon @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $entreprise->whatsapp_number) }}" placeholder="221781928588">
                  </div>
                  @error('whatsapp_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="form-text">Format international, sans « + » ni espaces.</div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="email" class="form-label">Email</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-envelope field-icon"></i>
                    <input type="email" class="form-control has-icon @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $entreprise->email) }}">
                  </div>
                  @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="website" class="form-label">Site web</label>
                  <div class="field-input-wrap">
                    <i class="bi bi-globe field-icon"></i>
                    <input type="text" class="form-control has-icon @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $entreprise->website) }}" placeholder="https://...">
                  </div>
                  @error('website')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="address_line1" class="form-label">Adresse ligne 1</label>
                  <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1', $entreprise->address_line1) }}">
                  @error('address_line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="address_line2" class="form-label">Adresse ligne 2</label>
                  <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" value="{{ old('address_line2', $entreprise->address_line2) }}" placeholder="Ville, pays">
                  @error('address_line2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          </div>

          {{-- ── Section : Mentions légales ─────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-legal">
              <span class="form-section__title"><i class="bi bi-patch-check"></i>Mentions légales</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-legal">
              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="ninea" class="form-label">NINEA</label>
                  <input type="text" class="form-control @error('ninea') is-invalid @enderror" id="ninea" name="ninea" value="{{ old('ninea', $entreprise->ninea) }}">
                  @error('ninea')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 field-group">
                  <label for="rccm" class="form-label">RCCM</label>
                  <input type="text" class="form-control @error('rccm') is-invalid @enderror" id="rccm" name="rccm" value="{{ old('rccm', $entreprise->rccm) }}">
                  @error('rccm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="form-text mb-0">Ces références apparaissent en pied de page de vos factures et devis.</div>
            </div>
          </div>

          {{-- ── Section : Apparence ────────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-apparence">
              <span class="form-section__title"><i class="bi bi-palette"></i>Apparence</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-apparence">
              <div class="row">
                <div class="col-md-6 field-group">
                  <label for="accent_color" class="form-label">Couleur primaire</label>
                  <div class="d-flex align-items-center gap-2">
                    <input type="color" class="form-control form-control-color @error('accent_color') is-invalid @enderror" id="accent_color" name="accent_color" value="{{ old('accent_color', $entreprise->accent_color ?? \App\Models\Entreprise::DEFAULT_ACCENT_COLOR) }}">
                    <code class="small text-muted" id="accentColorValue">{{ old('accent_color', $entreprise->accent_color ?? \App\Models\Entreprise::DEFAULT_ACCENT_COLOR) }}</code>
                  </div>
                  @error('accent_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="form-text">Sidebar, boutons, KPI principal et factures.</div>
                </div>
                <div class="col-md-6 field-group">
                  <label for="secondary_color" class="form-label">Couleur secondaire</label>
                  <div class="d-flex align-items-center gap-2">
                    <input type="color" class="form-control form-control-color @error('secondary_color') is-invalid @enderror" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $entreprise->secondary_color ?? \App\Models\Entreprise::DEFAULT_SECONDARY_COLOR) }}">
                    <code class="small text-muted" id="secondaryColorValue">{{ old('secondary_color', $entreprise->secondary_color ?? \App\Models\Entreprise::DEFAULT_SECONDARY_COLOR) }}</code>
                  </div>
                  @error('secondary_color')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="form-text">Dégradé du KPI principal, éléments secondaires.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- ── Section : Facturation ──────────────────────────── --}}
          <div class="form-section">
            <button type="button" class="form-section__header" data-toggle-section aria-expanded="true" aria-controls="section-facturation">
              <span class="form-section__title"><i class="bi bi-receipt"></i>Facturation</span>
              <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="form-section__body" id="section-facturation">
              <div class="field-group mb-0">
                <label for="invoice_footer_note" class="form-label">Note de bas de facture</label>
                <textarea class="form-control @error('invoice_footer_note') is-invalid @enderror" id="invoice_footer_note" name="invoice_footer_note" rows="3" placeholder="Ex : conditions de garantie, politique de retour...">{{ old('invoice_footer_note', $entreprise->invoice_footer_note) }}</textarea>
                @error('invoice_footer_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Affichée par défaut sur les factures, sauf si la vente a ses propres remarques.</div>
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

      <div id="entreprisePreviewBrand" class="rounded-4 p-4 mb-3" style="overflow:hidden; position:relative;">
        <div class="d-flex align-items-center gap-3" style="position:relative; z-index:1;">
          <div style="width:56px;height:56px;border-radius:14px;overflow:hidden;background:rgba(255,255,255,.15);flex-shrink:0;display:flex;align-items:center;justify-content:center;">
            <img id="entreprisePreviewLogo" src="{{ $entreprise->logo_url ?? asset('images/logo.jpeg') }}" alt="" style="width:100%;height:100%;object-fit:cover;{{ $entreprise->logo_url ? '' : 'display:none;' }}">
            <i id="entreprisePreviewLogoFallback" class="bi bi-building text-white fs-4" style="{{ $entreprise->logo_url ? 'display:none;' : '' }}"></i>
          </div>
          <div class="text-white">
            <div class="fw-bold" id="entreprisePreviewName" style="font-family:var(--font-display);">{{ $entreprise->name ?: 'Mon Entreprise' }}</div>
            <div class="small" style="color:rgba(255,255,255,.7);">Système d'information</div>
          </div>
        </div>
      </div>

      <button type="button" class="btn w-100 mb-2" id="entreprisePreviewButton" style="color:#fff;">
        <i class="bi bi-check-lg me-1"></i>Exemple de bouton
      </button>

      <p class="text-muted small mb-0">Ce mini-aperçu montre où vos couleurs et votre logo apparaissent — la sidebar et les factures utilisent exactement le même dégradé.</p>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  #entreprisePreviewBrand::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: .08;
    pointer-events: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1'%3E%3Cpath d='M0 50H35M35 0V35M35 35H70M70 35V0M70 65H100M35 35V100'/%3E%3C/g%3E%3Cg fill='%23ffffff'%3E%3Ccircle cx='35' cy='35' r='2'/%3E%3Ccircle cx='70' cy='35' r='2'/%3E%3Ccircle cx='70' cy='65' r='2'/%3E%3C/g%3E%3C/svg%3E");
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const logoInput = document.getElementById('logo');
    const primaryInput = document.getElementById('accent_color');
    const secondaryInput = document.getElementById('secondary_color');

    const previewName = document.getElementById('entreprisePreviewName');
    const previewBrand = document.getElementById('entreprisePreviewBrand');
    const previewLogo = document.getElementById('entreprisePreviewLogo');
    const previewLogoFallback = document.getElementById('entreprisePreviewLogoFallback');
    const previewButton = document.getElementById('entreprisePreviewButton');
    const accentValueEl = document.getElementById('accentColorValue');
    const secondaryValueEl = document.getElementById('secondaryColorValue');

    function updateColors() {
      const primary = primaryInput.value;
      const secondary = secondaryInput.value;
      previewBrand.style.background = `linear-gradient(135deg, ${primary} 0%, ${secondary} 100%)`;
      previewButton.style.background = primary;
      if (accentValueEl) accentValueEl.textContent = primary;
      if (secondaryValueEl) secondaryValueEl.textContent = secondary;
    }

    nameInput?.addEventListener('input', function () {
      previewName.textContent = nameInput.value || 'Mon Entreprise';
    });

    primaryInput?.addEventListener('input', updateColors);
    secondaryInput?.addEventListener('input', updateColors);

    logoInput?.addEventListener('change', function () {
      const file = logoInput.files?.[0];
      if (!file || !file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = function (e) {
        previewLogo.src = e.target.result;
        previewLogo.style.display = '';
        if (previewLogoFallback) previewLogoFallback.style.display = 'none';
      };
      reader.readAsDataURL(file);
    });

    updateColors();
  });
</script>
@endpush
