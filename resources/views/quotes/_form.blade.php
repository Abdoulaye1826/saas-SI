<div class="row">
  <div class="col-md-6 mb-3">
    <label for="customer_search" class="form-label">Client</label>

    <input type="hidden" id="customer_id" name="customer_id" value="{{ old('customer_id', $quote?->customer_id ?? '') }}">

    <div class="position-relative">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" class="form-control @error('customer_id') is-invalid @enderror"
               id="customer_search" autocomplete="off"
               placeholder="Tapez le nom, le téléphone ou l'email..."
               value="{{ old('customer_id', $quote?->customer_id ?? '') ? optional($quote?->customer ?? \App\Models\Customer::find(old('customer_id')))->full_name : '' }}">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newCustomerModal" title="Ajouter un client">
          <i class="bi bi-person-plus"></i>
        </button>
      </div>
      @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

      <div id="customerDropdown" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 250px; overflow-y: auto; display: none;"></div>
    </div>

    @php $selectedCustomer = old('customer_id', $quote?->customer_id ?? '') ? ($quote?->customer ?? \App\Models\Customer::find(old('customer_id'))) : null; @endphp
    <div id="customerSelected" class="alert alert-success d-flex align-items-center justify-content-between mt-2 py-2 px-3"
         style="display: {{ $selectedCustomer ? 'flex' : 'none' }} !important;">
      <span id="customerSelectedText">
        @if($selectedCustomer)
          <i class="bi bi-check-circle me-1"></i>
          <strong>{{ $selectedCustomer->full_name }}</strong>
          @if($selectedCustomer->phone) <span class="text-muted">({{ $selectedCustomer->phone }})</span> @endif
        @endif
      </span>
      <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="customerClear">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div id="customerNotFound" class="mt-2" style="display: none;">
      <div class="alert alert-warning py-2 px-3 d-flex align-items-center justify-content-between mb-0">
        <small><i class="bi bi-exclamation-triangle me-1"></i>Aucun client trouvé pour cette recherche.</small>
        <button type="button" class="btn btn-sm btn-primary" id="openNewCustomerModalFromSearch">
          <i class="bi bi-plus-circle me-1"></i>Ajouter un client
        </button>
      </div>
    </div>

    <div class="form-text">Laissez vide pour un client anonyme.</div>
  </div>

  <div class="col-md-3 mb-3">
    <label for="valid_until" class="form-label">Valable jusqu'au</label>
    <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
           id="valid_until" name="valid_until" value="{{ old('valid_until', $quote?->valid_until?->format('Y-m-d') ?? now()->addDays(15)->format('Y-m-d')) }}">
    @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 mb-3">
    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
      <option value="draft" @selected(old('status', $quote?->status->value ?? 'draft') === 'draft')>Brouillon</option>
      <option value="sent" @selected(old('status', $quote?->status->value ?? '') === 'sent')>Envoyé</option>
      <option value="accepted" @selected(old('status', $quote?->status->value ?? '') === 'accepted')>Accepté</option>
      <option value="refused" @selected(old('status', $quote?->status->value ?? '') === 'refused')>Refusé</option>
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
</div>

<div class="row">
  <div class="col-12 mb-3">
    <label class="form-label">Produits</label>
    <div class="card p-3">
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted">Ajoutez les produits du devis</div>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addQuoteItemButton">
            <i class="bi bi-plus-lg"></i> Ajouter un produit
          </button>
        </div>
        <div class="btn-group price-tier-group" role="group" aria-label="Tarif applicable" id="globalPriceTierGroup">
          <button type="button" class="btn btn-outline-secondary price-tier-btn active" data-tier="client">client</button>
          <button type="button" class="btn btn-outline-secondary price-tier-btn" data-tier="fournisseur">Revendeur</button>
        </div>
      </div>
      <div id="quoteItemsContainer">
        @php
          $oldProductIds = old('product_id', $quote?->items->pluck('product_id')->toArray() ?? []);
          $oldQuantities = old('quantity', $quote?->items->pluck('quantity')->toArray() ?? []);
          $oldUnitPrices = old('unit_price', $quote?->items->pluck('unit_price')->toArray() ?? []);

          $quoteItems = collect(is_array($oldProductIds) ? $oldProductIds : [$oldProductIds])
              ->map(function ($productId, $index) use ($oldQuantities, $oldUnitPrices) {
                  return [
                      'product_id' => $productId,
                      'quantity' => is_array($oldQuantities) ? ($oldQuantities[$index] ?? 1) : 1,
                      'unit_price' => is_array($oldUnitPrices) ? ($oldUnitPrices[$index] ?? 0) : ($oldUnitPrices ?? 0),
                  ];
              });

          if ($quoteItems->isEmpty()) {
              $quoteItems = collect([['product_id' => '', 'quantity' => 1, 'unit_price' => 0]]);
          }
        @endphp

        @foreach($quoteItems as $index => $quoteItem)
          <div class="quote-item-row row g-3 align-items-end mb-2" data-price-tier="client">
            <div class="col-md-5">
              <label class="form-label">Produit</label>
              <select name="product_id[]" class="form-select @error('product_id.' . $index) is-invalid @enderror" required>
                <option value="">— Sélectionnez un produit —</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}" @selected((int) $quoteItem['product_id'] === $product->id)>
                    {{ $product->reference }} — {{ $product->name }}
                  </option>
                @endforeach
              </select>
              @error('product_id.' . $index)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label class="form-label">Prix unitaire</label>
              <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control price-input @error('unit_price.' . $index) is-invalid @enderror"
                     value="{{ old('unit_price.' . $index, $quoteItem['unit_price'] ?? 0) }}" required>
              @error('unit_price.' . $index)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label class="form-label">Quantité</label>
              <input type="number" step="1" min="1" name="quantity[]" class="form-control @error('quantity.' . $index) is-invalid @enderror"
                     value="{{ $quoteItem['quantity'] }}" required>
              @error('quantity.' . $index)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label class="form-label">Total</label>
              <input type="text" class="form-control line-total" value="0" readonly>
            </div>
            <div class="col-md-1 text-end">
              <button type="button" class="btn btn-outline-danger btn-remove-item" style="margin-top: 32px;">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <template id="quoteItemTemplate">
    <div class="quote-item-row row g-3 align-items-end mb-2" data-price-tier="client">
      <div class="col-md-5">
        <label class="form-label">Produit</label>
        <select name="product_id[]" class="form-select" required>
          <option value="">— Sélectionnez un produit —</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->reference }} — {{ $product->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Prix unitaire</label>
        <input type="number" step="0.01" min="0" name="unit_price[]" class="form-control price-input" value="0" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Quantité</label>
        <input type="number" step="1" min="1" name="quantity[]" class="form-control" value="1" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Total</label>
        <input type="text" class="form-control line-total" value="0" readonly>
      </div>
      <div class="col-md-1 text-end">
        <button type="button" class="btn btn-outline-danger btn-remove-item" style="margin-top: 32px;">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>
  </template>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label for="discount_amount" class="form-label">Remise (FCFA)</label>
    <input type="number" step="0.01" min="0" class="form-control @error('discount_amount') is-invalid @enderror"
           id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $quote?->discount_amount ?? 0) }}">
    @error('discount_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4 mb-3">
    <label for="total_ttc" class="form-label">Total</label>
    <input type="text" class="form-control fw-bold" id="total_ttc" value="0" readonly>
    <div class="form-text">Calculé automatiquement à partir des produits et de la remise.</div>
  </div>
</div>

<div class="mb-3">
  <label for="notes" class="form-label">Observations</label>
  <textarea class="form-control @error('notes') is-invalid @enderror"
            id="notes" name="notes" rows="3">{{ old('notes', $quote->notes ?? '') }}</textarea>
  @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const quoteItemsContainer = document.getElementById('quoteItemsContainer');
    const quoteItemTemplate = document.getElementById('quoteItemTemplate');
    const addQuoteItemButton = document.getElementById('addQuoteItemButton');
    const productClientPrices = {
      @foreach($products as $product)
        {{ $product->id }}: {{ $product->sale_price }},
      @endforeach
    };
    const productSupplierPrices = {
      @foreach($products as $product)
        {{ $product->id }}: {{ $product->supplier_sale_price ?? $product->sale_price }},
      @endforeach
    };

    function calculateTotals() {
      const rows = quoteItemsContainer.querySelectorAll('.quote-item-row');
      let total = 0;

      rows.forEach(row => {
        const quantityInput = row.querySelector('input[name="quantity[]"]');
        const unitPriceInput = row.querySelector('input[name="unit_price[]"]');
        const lineTotalInput = row.querySelector('.line-total');

        const quantity = parseFloat(quantityInput?.value || 0) || 0;
        const unitPrice = parseFloat(unitPriceInput?.value || 0) || 0;
        const lineTotal = quantity * unitPrice;

        if (lineTotalInput) {
          lineTotalInput.value = lineTotal.toFixed(2).replace('.', ',');
        }

        total += lineTotal;
      });

      const discount = parseFloat(document.getElementById('discount_amount')?.value || 0) || 0;
      const totalField = document.getElementById('total_ttc');
      if (totalField) {
        totalField.value = Math.max(0, total - discount).toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' FCFA';
      }
    }

    let globalPriceTier = 'client';

    function applyPriceForTier(row) {
      const select = row.querySelector('select[name="product_id[]"]');
      const unitPriceInput = row.querySelector('.price-input');
      if (!select || !unitPriceInput) return;

      const productId = select.value;
      const prices = globalPriceTier === 'fournisseur' ? productSupplierPrices : productClientPrices;

      unitPriceInput.value = prices[productId] !== undefined ? Number(prices[productId]).toFixed(2) : 0;
      row.dataset.priceTier = globalPriceTier;
    }

    const globalPriceTierGroup = document.getElementById('globalPriceTierGroup');
    if (globalPriceTierGroup) {
      globalPriceTierGroup.querySelectorAll('.price-tier-btn').forEach(button => {
        button.addEventListener('click', function () {
          globalPriceTier = this.dataset.tier;
          globalPriceTierGroup.querySelectorAll('.price-tier-btn').forEach(btn => {
            btn.classList.toggle('active', btn === this);
          });

          quoteItemsContainer.querySelectorAll('.quote-item-row').forEach(row => {
            applyPriceForTier(row);
          });
          calculateTotals();
        });
      });
    }

    function bindQuoteItemEvents(container) {
      container.querySelectorAll('select[name="product_id[]"]').forEach(select => {
        select.addEventListener('change', function () {
          const row = this.closest('.quote-item-row');
          applyPriceForTier(row);
          calculateTotals();
        });
      });

      container.querySelectorAll('input[name="quantity[]"], input[name="unit_price[]"]').forEach(input => {
        input.addEventListener('input', calculateTotals);
      });

      container.querySelectorAll('.btn-remove-item').forEach(button => {
        button.addEventListener('click', function () {
          const row = this.closest('.quote-item-row');
          if (row) {
            row.remove();
            calculateTotals();
          }
        });
      });
    }

    if (addQuoteItemButton && quoteItemTemplate) {
      addQuoteItemButton.addEventListener('click', function () {
        const clone = quoteItemTemplate.content.cloneNode(true);
        quoteItemsContainer.appendChild(clone);
        bindQuoteItemEvents(quoteItemsContainer.lastElementChild);
        calculateTotals();
      });
    }

    const discountInput = document.getElementById('discount_amount');
    if (discountInput) discountInput.addEventListener('input', calculateTotals);

    bindQuoteItemEvents(quoteItemsContainer);
    calculateTotals();

    // ───────────────────────────────────────────────────────────────
    // Autocomplétion client (nom, téléphone, email) — même pattern que
    // le formulaire de vente.
    // ───────────────────────────────────────────────────────────────
    const customerSearchInput = document.getElementById('customer_search');
    const customerIdField = document.getElementById('customer_id');
    const customerDropdown = document.getElementById('customerDropdown');
    const customerSelected = document.getElementById('customerSelected');
    const customerSelectedText = document.getElementById('customerSelectedText');
    const customerNotFound = document.getElementById('customerNotFound');
    const customerClear = document.getElementById('customerClear');
    const openNewCustomerModalFromSearch = document.getElementById('openNewCustomerModalFromSearch');

    let customerSearchTimeout = null;
    let customerActiveIndex = -1;

    if (customerSearchInput) {
      customerSearchInput.addEventListener('input', function () {
        const query = this.value.trim();
        customerActiveIndex = -1;
        customerIdField.value = '';

        if (query.length < 2) {
          customerDropdown.style.display = 'none';
          customerNotFound.style.display = 'none';
          return;
        }

        clearTimeout(customerSearchTimeout);
        customerSearchTimeout = setTimeout(() => fetchCustomers(query), 300);
      });

      customerSearchInput.addEventListener('keydown', function (e) {
        const items = customerDropdown.querySelectorAll('.list-group-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          customerActiveIndex = Math.min(customerActiveIndex + 1, items.length - 1);
          updateActiveCustomerItem(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          customerActiveIndex = Math.max(customerActiveIndex - 1, 0);
          updateActiveCustomerItem(items);
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (customerActiveIndex >= 0 && items[customerActiveIndex]) {
            items[customerActiveIndex].click();
          }
        } else if (e.key === 'Escape') {
          customerDropdown.style.display = 'none';
        }
      });

      document.addEventListener('click', function (e) {
        if (!customerSearchInput.contains(e.target) && !customerDropdown.contains(e.target)) {
          customerDropdown.style.display = 'none';
        }
      });
    }

    function updateActiveCustomerItem(items) {
      items.forEach((item, idx) => item.classList.toggle('active', idx === customerActiveIndex));
      if (items[customerActiveIndex]) {
        items[customerActiveIndex].scrollIntoView({ block: 'nearest' });
      }
    }

    async function fetchCustomers(query) {
      try {
        const response = await fetch(`{{ route('sales.customers.search') }}?q=${encodeURIComponent(query)}`, {
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          }
        });

        const customers = await response.json();
        customerDropdown.innerHTML = '';

        if (customers.length === 0) {
          customerDropdown.style.display = 'none';
          customerNotFound.style.display = 'block';
          return;
        }

        customerNotFound.style.display = 'none';

        customers.forEach((customer) => {
          const item = document.createElement('button');
          item.type = 'button';
          item.className = 'list-group-item list-group-item-action py-2 px-3';
          item.innerHTML = `
            <div>
              <strong>${customer.full_name}</strong>
              ${customer.phone ? '<span class="text-muted">— ' + customer.phone + '</span>' : ''}
              ${customer.email ? '<br><small class="text-muted">' + customer.email + '</small>' : ''}
            </div>
          `;
          item.addEventListener('click', () => selectCustomer(customer));
          customerDropdown.appendChild(item);
        });

        customerDropdown.style.display = 'block';
      } catch (error) {
        console.error('Erreur lors de la recherche de clients :', error);
      }
    }

    function selectCustomer(customer) {
      customerIdField.value = customer.id;
      customerSearchInput.value = customer.full_name;
      customerDropdown.style.display = 'none';
      customerNotFound.style.display = 'none';

      customerSelectedText.innerHTML = `
        <i class="bi bi-check-circle me-1"></i>
        <strong>${customer.full_name}</strong>
        ${customer.phone ? '<span class="text-muted">(' + customer.phone + ')</span>' : ''}
      `;
      customerSelected.style.display = 'flex';
    }

    if (customerClear) {
      customerClear.addEventListener('click', function () {
        customerIdField.value = '';
        customerSearchInput.value = '';
        customerSelected.style.display = 'none';
        customerSelectedText.innerHTML = '';
        customerSearchInput.focus();
      });
    }

    const saveCustomerButton = document.getElementById('saveNewCustomerButton');
    const newCustomerForm = document.getElementById('newCustomerForm');

    if (openNewCustomerModalFromSearch) {
      openNewCustomerModalFromSearch.addEventListener('click', function () {
        const nameField = document.getElementById('new_customer_full_name');
        if (nameField && customerSearchInput.value.trim()) {
          nameField.value = customerSearchInput.value.trim();
        }
        const modalEl = document.getElementById('newCustomerModal');
        new bootstrap.Modal(modalEl).show();
      });
    }

    if (saveCustomerButton && newCustomerForm) {
      saveCustomerButton.addEventListener('click', async function () {
        const formData = new FormData(newCustomerForm);

        document.querySelectorAll('#newCustomerForm .invalid-feedback').forEach(el => {
          el.textContent = '';
        });
        document.querySelectorAll('#newCustomerForm .is-invalid').forEach(el => {
          el.classList.remove('is-invalid');
        });

        const response = await fetch('{{ route('customers.store') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
          if (data.errors) {
            Object.entries(data.errors).forEach(([field, messages]) => {
              const input = document.getElementById(`new_customer_${field}`);
              const feedback = document.getElementById(`new_customer_${field}_error`);
              if (input) {
                input.classList.add('is-invalid');
              }
              if (feedback) {
                feedback.textContent = messages.join(' ');
              }
            });
          }
          return;
        }

        selectCustomer(data);

        const modal = bootstrap.Modal.getInstance(document.getElementById('newCustomerModal'));
        modal.hide();
        newCustomerForm.reset();
      });
    }
  });
</script>
@endpush
