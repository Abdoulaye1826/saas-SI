<div class="field-group">
  <label for="name" class="form-label">Nom <span class="req">*</span></label>
  <div class="field-input-wrap">
    <i class="bi bi-bookmark field-icon"></i>
    <input type="text" class="form-control has-icon @error('name') is-invalid @enderror"
           id="name" name="name" value="{{ old('name', $category->name ?? '') }}"
           placeholder="Ex : Consoles, Accessoires, Jeux vidéo..." required>
    <i class="bi bi-check-circle-fill valid-feedback-icon"></i>
    <i class="bi bi-exclamation-circle-fill invalid-feedback-icon"></i>
  </div>
  @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<div class="field-group">
  <label for="description" class="form-label">Description</label>
  <textarea class="form-control @error('description') is-invalid @enderror"
            id="description" name="description" rows="3"
            placeholder="Quelques mots pour décrire cette catégorie...">{{ old('description', $category->description ?? '') }}</textarea>
  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="field-group">
  <label class="form-label">Disponibilité</label>
  <div class="form-check form-switch fs-6 ps-1">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" role="switch"
           {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Catégorie active</label>
  </div>
  <div class="form-text">Désactivez-la pour la masquer des formulaires sans supprimer les produits associés.</div>
</div>

{{-- ── Boutique en ligne ──────────────────────────────────── --}}
<div class="field-group">
  <label class="form-label">Boutique en ligne</label>
  <div class="form-check form-switch fs-6 ps-1">
    <input class="form-check-input" type="checkbox" id="show_on_store" name="show_on_store" value="1" role="switch"
           {{ old('show_on_store', $category->show_on_store ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="show_on_store">Afficher sur la boutique</label>
  </div>
  <div class="form-text">Cette catégorie apparaît dans la navigation et les filtres de la boutique publique.</div>
</div>

<div class="row align-items-start">
  <div class="col-md-7 field-group mb-md-0">
    <label for="image" class="form-label">Image de catégorie</label>
    <label class="image-dropzone" for="image" tabindex="0">
      <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/webp">
      <div class="image-dropzone__icon"><i class="bi bi-cloud-arrow-up"></i></div>
      <div class="image-dropzone__text"><strong>Cliquez</strong> ou glissez-déposez une image ici<br>JPG, PNG ou WEBP</div>
    </label>
    @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

    @if(isset($category) && $category->image_url)
      <div class="image-preview" style="display:flex">
        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy">
        <button type="button" class="image-preview__remove" data-remove-target="remove_image">
          <i class="bi bi-trash me-1"></i>Supprimer l'image
        </button>
        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="d-none">
      </div>
    @else
      <div class="image-preview" style="display:none"></div>
    @endif
    <div class="form-text">Illustration affichée sur la page d'accueil et la navigation de la boutique.</div>
  </div>
  <div class="col-md-5 field-group mb-0">
    <label for="sort_order" class="form-label">Ordre d'affichage</label>
    <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror"
           id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Les catégories s'affichent par ordre croissant sur la boutique.</div>
  </div>
</div>
