<div class="field-group">
  <label for="label" class="form-label">Libellé <span class="req">*</span></label>
  <input type="text" class="form-control @error('label') is-invalid @enderror"
         id="label" name="label" value="{{ old('label', $menu->label ?? '') }}"
         placeholder="Ex : Promotions" required>
  @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="field-group">
  <label for="url" class="form-label">Lien <span class="req">*</span></label>
  <input type="text" class="form-control @error('url') is-invalid @enderror"
         id="url" name="url" value="{{ old('url', $menu->url ?? '') }}"
         placeholder="/boutique/produits?is_promo=1" required>
  @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
  <div class="form-text">Chemin relatif (ex : /boutique/page/cgv) ou lien complet (https://...).</div>
</div>

<div class="row">
  <div class="col-md-6 field-group">
    <label for="sort_order" class="form-label">Ordre</label>
    <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror"
           id="sort_order" name="sort_order" value="{{ old('sort_order', $menu->sort_order ?? 0) }}">
    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row">
  <div class="col-md-6 field-group">
    <div class="form-check form-switch fs-6 ps-1">
      <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" role="switch"
             {{ old('is_active', $menu->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">Actif</label>
    </div>
  </div>
  <div class="col-md-6 field-group mb-0">
    <div class="form-check form-switch fs-6 ps-1">
      <input class="form-check-input" type="checkbox" id="opens_new_tab" name="opens_new_tab" value="1" role="switch"
             {{ old('opens_new_tab', $menu->opens_new_tab ?? false) ? 'checked' : '' }}>
      <label class="form-check-label" for="opens_new_tab">Ouvrir dans un nouvel onglet</label>
    </div>
  </div>
</div>
