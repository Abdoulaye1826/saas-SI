<div class="field-group">
  <label for="title" class="form-label">Titre <span class="req">*</span></label>
  <input type="text" class="form-control @error('title') is-invalid @enderror"
         id="title" name="title" value="{{ old('title', $page->title ?? '') }}"
         placeholder="Ex : À propos" required>
  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="field-group">
  <label for="slug" class="form-label">Slug (URL)</label>
  <input type="text" class="form-control @error('slug') is-invalid @enderror"
         id="slug" name="slug" value="{{ old('slug', $page->slug ?? '') }}"
         placeholder="Généré automatiquement si vide">
  @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
  <div class="form-text">URL publique : /boutique/page/{{ old('slug', $page->slug ?? 'votre-slug') }}</div>
</div>

<div class="field-group">
  <label for="content" class="form-label">Contenu</label>
  <textarea class="form-control @error('content') is-invalid @enderror"
            id="content" name="content" rows="10">{{ old('content', $page->content ?? '') }}</textarea>
  @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
  <div class="form-text">Texte simple, sans mise en forme — les sauts de ligne sont conservés à l'affichage.</div>
</div>

<div class="row">
  <div class="col-md-6 field-group">
    <label for="meta_title" class="form-label">Meta title (SEO)</label>
    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
           id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}">
    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6 field-group">
    <label for="meta_description" class="form-label">Meta description (SEO)</label>
    <input type="text" class="form-control @error('meta_description') is-invalid @enderror"
           id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description ?? '') }}">
    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row">
  <div class="col-md-6 field-group">
    <div class="form-check form-switch fs-6 ps-1">
      <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" role="switch"
             {{ old('is_published', $page->is_published ?? false) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_published">Page publiée</label>
    </div>
    <div class="form-text">Invisible côté boutique tant que ce n'est pas coché.</div>
  </div>
  <div class="col-md-6 field-group mb-0">
    <div class="form-check form-switch fs-6 ps-1">
      <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" value="1" role="switch"
             {{ old('show_in_footer', $page->show_in_footer ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="show_in_footer">Afficher dans le pied de page</label>
    </div>
  </div>
</div>
