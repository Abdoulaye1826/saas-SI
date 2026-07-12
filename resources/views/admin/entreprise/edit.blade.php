@extends('layouts.dashboard')

@section('title', "Informations de l'entreprise")
@section('page-title', "Informations de l'entreprise")

@section('content')
<div class="page-header">
  <h1><i class="bi bi-building me-2"></i>Informations de l'entreprise</h1>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('admin.entreprise.update') }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <div class="row">
        <div class="col-md-12 mb-3">
          <label class="form-label">Logo actuel</label><br>
          @if($entreprise->logo_url)
            <img src="{{ $entreprise->logo_url }}" alt="{{ $entreprise->name }}" style="height:80px;width:80px;object-fit:cover;border-radius:50%;border:1px solid #dee2e6;">
          @else
            <p class="text-muted mb-0">Aucun logo</p>
          @endif
          <input type="file" class="form-control mt-2 @error('logo') is-invalid @enderror" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp">
          @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Nom commercial <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $entreprise->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label for="legal_name" class="form-label">Raison sociale</label>
          <input type="text" class="form-control @error('legal_name') is-invalid @enderror" id="legal_name" name="legal_name" value="{{ old('legal_name', $entreprise->legal_name) }}">
          @error('legal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="phone" class="form-label">Téléphone</label>
          <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $entreprise->phone) }}">
          @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label for="whatsapp_number" class="form-label">WhatsApp (format international, sans +)</label>
          <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $entreprise->whatsapp_number) }}" placeholder="221781928588">
          @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $entreprise->email) }}">
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label for="website" class="form-label">Site web</label>
          <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $entreprise->website) }}">
          @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="address_line1" class="form-label">Adresse ligne 1</label>
          <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" value="{{ old('address_line1', $entreprise->address_line1) }}">
          @error('address_line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
          <label for="address_line2" class="form-label">Adresse ligne 2</label>
          <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" value="{{ old('address_line2', $entreprise->address_line2) }}">
          @error('address_line2')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label for="ninea" class="form-label">NINEA</label>
          <input type="text" class="form-control @error('ninea') is-invalid @enderror" id="ninea" name="ninea" value="{{ old('ninea', $entreprise->ninea) }}">
          @error('ninea')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
          <label for="rccm" class="form-label">RCCM</label>
          <input type="text" class="form-control @error('rccm') is-invalid @enderror" id="rccm" name="rccm" value="{{ old('rccm', $entreprise->rccm) }}">
          @error('rccm')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
          <label for="accent_color" class="form-label">Couleur de marque</label>
          <input type="color" class="form-control form-control-color @error('accent_color') is-invalid @enderror" id="accent_color" name="accent_color" value="{{ old('accent_color', $entreprise->accent_color ?? '#1e3a5f') }}">
          @error('accent_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 mb-3">
          <label for="invoice_footer_note" class="form-label">Note de bas de facture</label>
          <textarea class="form-control @error('invoice_footer_note') is-invalid @enderror" id="invoice_footer_note" name="invoice_footer_note" rows="3">{{ old('invoice_footer_note', $entreprise->invoice_footer_note) }}</textarea>
          @error('invoice_footer_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="d-flex gap-2 mt-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
      </div>
    </form>
  </div>
</div>
@endsection
