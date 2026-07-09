{{-- Tableaux "activité récente" / classements du tableau de bord.
     Certains (mouvements de stock, produits les plus vendus, alertes stock)
     sont bornés à la période via DashboardService ; les listes "récentes"
     (devis/factures/mouvements) restent volontairement non filtrées — ce
     sont des flux d'activité, pas des agrégats statistiques. --}}

@unless($isCashier)
<div class="row g-3 mb-4">
  <div class="col-lg-12">
    <div class="table-card h-100">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-down-up me-2"></i>Derniers mouvements de stock</h6>
      </div>
      <div class="table-responsive" style="max-height: 320px;">
        <table class="table table-hover mb-0 small">
          <thead>
            <tr>
              <th>Produit</th>
              <th>Type</th>
              <th class="text-end">Qté</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentStockMovements as $movement)
              <tr>
                <td>{{ $movement->product?->name ?? '—' }}</td>
                <td>
                  @php
                    $movementBadge = match($movement->type->value ?? $movement->type) {
                      'entry' => 'bg-success',
                      'exit' => 'bg-danger',
                      'sale' => 'bg-primary',
                      'return' => 'bg-warning text-dark',
                      default => 'bg-secondary',
                    };
                  @endphp
                  <span class="badge {{ $movementBadge }}">{{ $movement->type->label() }}</span>
                </td>
                <td class="text-end">{{ $movement->quantity_before }} → {{ $movement->quantity_after }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Aucun mouvement de stock</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endunless

{{-- Devis récents + Factures récentes --}}
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="table-card h-100">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-ruled me-2"></i>Devis récents</h6>
        <a href="{{ route('quotes.index') }}" class="small text-decoration-none">Voir tout</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Numéro</th>
              <th>Client</th>
              <th class="text-end">Montant</th>
              <th class="text-end">Statut</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentQuotes as $quote)
              <tr>
                <td>{{ $quote->quote_number }}</td>
                <td>{{ $quote->customer?->full_name ?? '—' }}</td>
                <td class="text-end">{{ number_format($quote->total_ttc, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">
                  <span class="badge {{ $quote->status->badgeClass() }}">{{ $quote->status->label() }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Aucun devis récent</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="table-card h-100">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Factures récentes</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Numéro</th>
              <th>Client</th>
              <th class="text-end">Montant</th>
              <th class="text-end">Statut</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentInvoices as $invoice)
              @php
                $invoiceStatus = $invoice->status instanceof App\Enums\InvoiceStatus
                    ? $invoice->status
                    : App\Enums\InvoiceStatus::from($invoice->status);
              @endphp
              <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->customer?->full_name ?? '—' }}</td>
                <td class="text-end">{{ number_format($invoice->total_ttc, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">
                  <span class="badge {{ $invoiceStatus === App\Enums\InvoiceStatus::Paid ? 'bg-success' : ($invoiceStatus === App\Enums\InvoiceStatus::Issued ? 'bg-warning text-dark' : 'bg-danger') }}">
                    {{ $invoiceStatus->label() }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Aucune facture récente</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Top clients + Vendeurs performants (bornés à la période) --}}
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="table-card h-100">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-trophy me-2"></i>Top clients</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Client</th>
              <th class="text-center">Factures</th>
              <th class="text-end">Montant</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topCustomers as $index => $customer)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $customer->full_name }}</td>
                <td class="text-center"><span class="badge bg-primary">{{ $customer->invoices_count }}</span></td>
                <td class="text-end">{{ number_format($customer->total_amount, 0, ',', ' ') }} FCFA</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Aucun client n'a encore passé de commande sur cette période</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="table-card h-100">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-people-fill me-2"></i>Vendeurs performants</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Vendeur</th>
              <th class="text-center">Ventes</th>
              <th class="text-end">Montant</th>
            </tr>
          </thead>
          <tbody>
            @forelse($salesByUser as $index => $user)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td class="text-center"><span class="badge bg-info">{{ $user->sales_count }}</span></td>
                <td class="text-end">{{ number_format($user->total_amount, 0, ',', ' ') }} FCFA</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Aucun vendeur enregistré sur cette période</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Top produits + Alertes stock — masqués pour le caissier --}}
@unless($isCashier)
<div class="row g-3">
  <div class="col-lg-7">
    <div class="table-card">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-trophy me-2"></i>Produits les plus vendus</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Produit</th>
              <th class="text-center">Qté vendue</th>
              <th class="text-end">Montant</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topProducts as $index => $product)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td class="text-center"><span class="badge bg-primary">{{ $product->total_qty }}</span></td>
                <td class="text-end">{{ number_format($product->total_amount, 0, ',', ' ') }} FCFA</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">Aucune vente enregistrée sur cette période</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="table-card">
      <div class="p-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle me-2"></i>Alertes stock</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Produit</th>
              <th class="text-center">Stock</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stockAlerts as $product)
              <tr>
                <td>
                  <div class="fw-medium">{{ $product->name }}</div>
                  <small class="text-muted">{{ $product->category?->name }}</small>
                </td>
                <td class="text-center">{{ $product->stock_quantity }}</td>
                <td>
                  @if($product->isOutOfStock())
                    <span class="badge bg-danger">Rupture</span>
                  @else
                    <span class="badge bg-warning text-dark">Stock faible</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Aucune alerte stock</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endunless
