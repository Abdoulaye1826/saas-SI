@extends('layouts.dashboard')

@section('title', $product->name)
@section('page-title', 'Détail produit')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
  <div>
    <h1>{{ $product->name }}</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produits</a></li>
        <li class="breadcrumb-item active">{{ $product->reference }}</li>
      </ol>
    </nav>
  </div>
  <div class="d-flex gap-2">
    @if($product->is_active && !$product->isOutOfStock())
      <a href="{{ route('sales.create', ['product_id' => $product->id]) }}" class="btn btn-success">
        <i class="bi bi-cart-plus me-1"></i>Vendre
      </a>
    @else
      <button type="button" class="btn btn-success" disabled
              title="@if(!$product->is_active) Produit inactif @else Rupture de stock @endif">
        <i class="bi bi-cart-plus me-1"></i>Vendre
      </button>
    @endif
    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
      <i class="bi bi-pencil me-1"></i>Modifier
    </a>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Retour</a>
  </div>
</div>

<div class="row g-4 u-animate">
  <div class="col-lg-4">
    <div class="detail-hero">
      @if($product->image)
        <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="detail-hero__image" loading="lazy">
      @else
        <div class="detail-hero__placeholder">
          <i class="bi bi-controller"></i>
        </div>
      @endif
      <h2>{{ $product->name }}</h2>
      <div class="detail-hero__sub">{{ $product->brand ?? 'Marque non renseignée' }}</div>
      <div class="detail-hero__badges">
        <span class="badge bg-info">{{ $product->category?->name ?? 'Sans catégorie' }}</span>
        @if($product->is_active)
          <span class="badge bg-success">Actif</span>
        @else
          <span class="badge bg-secondary">Inactif</span>
        @endif
        @if($product->isOutOfStock())
          <span class="badge bg-danger">Rupture</span>
        @elseif($product->isLowStock())
          <span class="badge bg-warning text-dark">Stock faible</span>
        @endif
        @if($product->tracks_imei)
          <span class="badge bg-primary"><i class="bi bi-phone me-1"></i>Suivi IMEI</span>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="detail-card mb-4">
      <div class="detail-card__header"><i class="bi bi-info-circle"></i>Informations</div>
      <div class="detail-card__body">
        <div class="detail-stat-grid mb-4">
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-upc"></i>Référence</div>
            <div class="detail-stat__value" style="font-size:.95rem;">{{ $product->reference }}</div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-upc-scan"></i>Code-barres</div>
            <div class="detail-stat__value" style="font-size:.95rem;">{{ $product->barcode ?? '—' }}</div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-truck"></i>Fournisseur</div>
            <div class="detail-stat__value" style="font-size:.95rem;">{{ $product->supplier?->name ?? '—' }}</div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-boxes"></i>Stock</div>
            <div class="detail-stat__value {{ $product->isOutOfStock() ? 'text-danger' : '' }} d-flex align-items-center gap-2 flex-wrap">
              <span id="stockQuantityDisplay">{{ $product->stock_quantity }}</span>
              <span class="text-muted" style="font-size:.75rem;">/ min {{ $product->minimum_stock }}</span>
              @if($product->tracks_imei)
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#imeiAddModal" title="Ajouter un IMEI">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#imeiRemoveModal" title="Retirer un IMEI">
                    <i class="bi bi-dash-lg"></i>
                  </button>
                </div>
              @else
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-success" data-stock-direction="in" data-bs-toggle="modal" data-bs-target="#stockAdjustModal" title="Ajouter du stock">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" data-stock-direction="out" data-bs-toggle="modal" data-bs-target="#stockAdjustModal" title="Retirer du stock">
                    <i class="bi bi-dash-lg"></i>
                  </button>
                </div>
              @endif
            </div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-cash"></i>Prix achat</div>
            <div class="detail-stat__value">{{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</div>
          </div>
          <div class="detail-stat">
            <div class="detail-stat__label"><i class="bi bi-tag"></i>Prix vente</div>
            <div class="detail-stat__value text-copper">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</div>
          </div>
          <div class="detail-stat detail-stat--accent">
            <div class="detail-stat__label"><i class="bi bi-graph-up-arrow"></i>Marge</div>
            <div class="detail-stat__value {{ $product->margin < 0 ? 'text-danger' : 'text-success' }}">
              {{ number_format($product->margin, 0, ',', ' ') }} FCFA
              <span style="font-size:.8rem;font-weight:600;">({{ $product->margin_rate }}%)</span>
            </div>
          </div>
        </div>

        @if($product->description)
          <div>
            <div class="detail-stat__label mb-2"><i class="bi bi-card-text"></i>Description</div>
            <p class="mb-0" style="color:var(--text);line-height:1.6;">{{ $product->description }}</p>
          </div>
        @endif
      </div>
    </div>

    @if($product->tracks_imei)
      <div class="detail-card mb-4">
        <div class="detail-card__header"><i class="bi bi-phone"></i>Historique des IMEI</div>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>IMEI</th>
                <th>Statut</th>
                <th>Entré le</th>
                <th>Vendu le</th>
                <th>Client</th>
                <th>Facture</th>
              </tr>
            </thead>
            <tbody>
              @forelse($product->imeis as $imei)
                <tr>
                  <td class="font-monospace">{{ $imei->imei }}</td>
                  <td><span class="badge {{ $imei->status->badgeClass() }}">{{ $imei->status->label() }}</span></td>
                  <td>
                    {{ $imei->created_at->format('d/m/Y') }}
                    @if($imei->exchangeSale)
                      <br><small class="text-muted">Échange {{ $imei->exchangeSale->exchange_voucher_number }}</small>
                    @endif
                  </td>
                  <td>{{ $imei->sold_at?->format('d/m/Y') ?? '—' }}</td>
                  <td>{{ $imei->sale?->customer?->full_name ?? '—' }}</td>
                  <td>
                    @if($imei->sale?->invoice)
                      <a href="{{ route('invoices.print', $imei->sale->invoice) }}" target="_blank">{{ $imei->sale->invoice->invoice_number }}</a>
                    @else
                      —
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Aucun IMEI enregistré pour le moment.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @endif

    @if($product->stockMovements->isNotEmpty())
      @include('products.partials.stock-movements')
    @endif
  </div>
</div>

@unless($product->tracks_imei)
  {{-- ── Ajustement manuel du stock (+/-) — produits sans suivi IMEI ── --}}
  <div class="modal fade" id="stockAdjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="stockAdjustModalTitle">Ajuster le stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="stockAdjustQuantity">Quantité</label>
            <input type="number" min="1" step="1" class="form-control" id="stockAdjustQuantity" value="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="stockAdjustReason">Motif (optionnel)</label>
            <input type="text" class="form-control" id="stockAdjustReason" maxlength="255" placeholder="Ex. : réception fournisseur, casse, inventaire...">
          </div>
          <div class="invalid-feedback" id="stockAdjustError"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-primary" id="stockAdjustSubmit">Valider</button>
        </div>
      </div>
    </div>
  </div>
@else
  {{-- ── Ajout d'un IMEI ── --}}
  <div class="modal fade" id="imeiAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un IMEI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <label class="form-label" for="imeiAddInput">IMEI</label>
          <input type="text" class="form-control" id="imeiAddInput" placeholder="Saisir ou scanner l'IMEI (14 à 17 chiffres)" inputmode="numeric" autocomplete="off">
          <div class="invalid-feedback" id="imeiAddError"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-primary" id="imeiAddSubmit">Ajouter</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Retrait d'un IMEI disponible ── --}}
  <div class="modal fade" id="imeiRemoveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Retirer un IMEI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          @php $availableImeis = $product->imeis->where('status', \App\Enums\ImeiStatus::Available); @endphp
          @if($availableImeis->isEmpty())
            <p class="text-muted mb-0">Aucun IMEI disponible à retirer.</p>
          @else
            <label class="form-label" for="imeiRemoveSelect">IMEI à retirer</label>
            <select class="form-select" id="imeiRemoveSelect">
              @foreach($availableImeis as $availableImei)
                <option value="{{ route('imeis.destroy', $availableImei) }}">{{ $availableImei->imei }}</option>
              @endforeach
            </select>
          @endif
          <div class="invalid-feedback" id="imeiRemoveError"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          @if($availableImeis->isNotEmpty())
            <button type="button" class="btn btn-danger" id="imeiRemoveSubmit">Retirer</button>
          @endif
        </div>
      </div>
    </div>
  </div>
@endunless

@push('scripts')
<script>
(function () {
  'use strict';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const stockQuantityDisplay = document.getElementById('stockQuantityDisplay');

  // L'icône d'alerte (::before en CSS) ne doit apparaître que lorsqu'il y a
  // réellement un message d'erreur — jamais par défaut sur une modale vierge.
  function setFieldError(el, message) {
    el.textContent = message || '';
    el.classList.toggle('d-block', Boolean(message));
  }

  @unless($product->tracks_imei)
    // ── Ajustement manuel du stock (+/-) ──
    const stockAdjustModalEl = document.getElementById('stockAdjustModal');
    const stockAdjustModal = new bootstrap.Modal(stockAdjustModalEl);
    const stockAdjustTitle = document.getElementById('stockAdjustModalTitle');
    const stockAdjustQuantity = document.getElementById('stockAdjustQuantity');
    const stockAdjustReason = document.getElementById('stockAdjustReason');
    const stockAdjustError = document.getElementById('stockAdjustError');
    const stockAdjustSubmit = document.getElementById('stockAdjustSubmit');
    let stockAdjustDirection = 'in';

    stockAdjustModalEl.addEventListener('show.bs.modal', function (event) {
      stockAdjustDirection = event.relatedTarget?.dataset.stockDirection === 'out' ? 'out' : 'in';
      stockAdjustTitle.textContent = stockAdjustDirection === 'in' ? 'Ajouter du stock' : 'Retirer du stock';
      stockAdjustSubmit.className = stockAdjustDirection === 'in' ? 'btn btn-success' : 'btn btn-danger';
      stockAdjustQuantity.value = 1;
      stockAdjustReason.value = '';
      setFieldError(stockAdjustError, '');
    });

    stockAdjustSubmit.addEventListener('click', async function () {
      const quantity = parseInt(stockAdjustQuantity.value, 10);
      setFieldError(stockAdjustError, '');

      if (!quantity || quantity < 1) {
        setFieldError(stockAdjustError, 'Veuillez saisir une quantité valide.');
        return;
      }

      stockAdjustSubmit.disabled = true;

      try {
        const response = await fetch('{{ route('products.stock.adjust', $product) }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            direction: stockAdjustDirection,
            quantity: quantity,
            reason: stockAdjustReason.value.trim() || null,
          }),
        });

        const data = await response.json();

        if (!response.ok) {
          setFieldError(stockAdjustError, data.error || 'Erreur lors de la mise à jour du stock.');
          return;
        }

        if (stockQuantityDisplay && data.stock_quantity !== undefined) {
          stockQuantityDisplay.textContent = data.stock_quantity;
        }

        stockAdjustModal.hide();
        if (window.UiToast) {
          window.UiToast.show('Stock mis à jour avec succès.', 'success');
        }
        setTimeout(() => window.location.reload(), 600);
      } catch (error) {
        setFieldError(stockAdjustError, 'Erreur réseau lors de la mise à jour du stock.');
      } finally {
        stockAdjustSubmit.disabled = false;
      }
    });
  @else
    // ── Ajout d'un IMEI ──
    const imeiAddModalEl = document.getElementById('imeiAddModal');
    const imeiAddModal = new bootstrap.Modal(imeiAddModalEl);
    const imeiAddInput = document.getElementById('imeiAddInput');
    const imeiAddError = document.getElementById('imeiAddError');
    const imeiAddSubmit = document.getElementById('imeiAddSubmit');

    imeiAddModalEl.addEventListener('show.bs.modal', function () {
      imeiAddInput.value = '';
      setFieldError(imeiAddError, '');
    });

    // Une douchette code-barres/QR envoie le code puis "Entrée" : on en
    // profite pour déclencher directement l'ajout, sans clic supplémentaire
    // (scan → ajouté), plutôt que de laisser l'Entrée sans effet.
    imeiAddInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        imeiAddSubmit.click();
      }
    });

    imeiAddSubmit.addEventListener('click', async function () {
      const imei = imeiAddInput.value.trim();
      setFieldError(imeiAddError, '');

      if (!imei) {
        setFieldError(imeiAddError, 'Veuillez saisir un IMEI.');
        return;
      }

      imeiAddSubmit.disabled = true;

      try {
        const response = await fetch('{{ route('products.imeis.store', $product) }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ imeis: [imei] }),
        });

        const data = await response.json();

        if (!response.ok) {
          setFieldError(imeiAddError, data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Erreur lors de l\'ajout.'));
          return;
        }

        if (stockQuantityDisplay && data.stock_quantity !== undefined) {
          stockQuantityDisplay.textContent = data.stock_quantity;
        }

        imeiAddModal.hide();
        if (window.UiToast) {
          window.UiToast.show('IMEI ajouté avec succès.', 'success');
        }
        setTimeout(() => window.location.reload(), 600);
      } catch (error) {
        setFieldError(imeiAddError, 'Erreur réseau lors de l\'ajout.');
      } finally {
        imeiAddSubmit.disabled = false;
      }
    });

    // ── Retrait d'un IMEI disponible ──
    const imeiRemoveSubmit = document.getElementById('imeiRemoveSubmit');
    if (imeiRemoveSubmit) {
      const imeiRemoveModalEl = document.getElementById('imeiRemoveModal');
      const imeiRemoveModal = new bootstrap.Modal(imeiRemoveModalEl);
      const imeiRemoveSelect = document.getElementById('imeiRemoveSelect');
      const imeiRemoveError = document.getElementById('imeiRemoveError');

      imeiRemoveSubmit.addEventListener('click', async function () {
        const url = imeiRemoveSelect.value;
        setFieldError(imeiRemoveError, '');

        if (!url) return;

        imeiRemoveSubmit.disabled = true;

        try {
          const response = await fetch(url, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
          });

          const data = await response.json();

          if (!response.ok) {
            setFieldError(imeiRemoveError, data.error || 'Erreur lors du retrait.');
            return;
          }

          if (stockQuantityDisplay && data.stock_quantity !== undefined) {
            stockQuantityDisplay.textContent = data.stock_quantity;
          }

          imeiRemoveModal.hide();
          if (window.UiToast) {
            window.UiToast.show('IMEI retiré avec succès.', 'success');
          }
          setTimeout(() => window.location.reload(), 600);
        } catch (error) {
          setFieldError(imeiRemoveError, 'Erreur réseau lors du retrait.');
        } finally {
          imeiRemoveSubmit.disabled = false;
        }
      });
    }
  @endunless
})();
</script>
@endpush
@endsection
