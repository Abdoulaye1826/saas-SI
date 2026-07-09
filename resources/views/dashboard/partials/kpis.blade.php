{{-- Grille compacte des KPI essentiels du tableau de bord (8 à 12
     indicateurs, volontairement limitée pour rester lisible en un coup
     d'œil), bornés à la période sélectionnée. Le détail complet (facturation,
     panier moyen, marge, devis, valeur du stock...) est disponible sur la
     page Rapports. Rechargée entièrement (innerHTML) à chaque changement de
     période. --}}
@php
  $p = $stats['period'];

  $kpis = [
    ['label' => 'Chiffre d\'affaires', 'value' => number_format($p['revenue'], 0, ',', ' ') . ' FCFA', 'icon' => 'bi-currency-exchange', 'color' => 'bg-primary bg-opacity-10 text-primary'],
    ['label' => 'Nombre de ventes',    'value' => $p['sales_count'],                                     'icon' => 'bi-cart-check',         'color' => 'bg-info bg-opacity-10 text-info'],
    ['label' => 'Montant encaissé',    'value' => number_format($p['amount_paid'], 0, ',', ' ') . ' FCFA', 'icon' => 'bi-cash-stack',        'color' => 'bg-success bg-opacity-10 text-success'],
    ['label' => 'Reste à payer',       'value' => number_format($p['remaining_amount'], 0, ',', ' ') . ' FCFA', 'icon' => 'bi-exclamation-circle', 'color' => 'bg-danger bg-opacity-10 text-danger'],
    ['label' => 'Produits vendus',     'value' => $p['products_sold_qty'], 'icon' => 'bi-box-seam', 'color' => 'bg-primary bg-opacity-10 text-primary'],
    ...($isCashier ? [] : [
      ['label' => 'Produits en rupture',    'value' => $stats['out_of_stock_count'], 'icon' => 'bi-x-octagon',            'color' => 'bg-danger bg-opacity-10 text-danger'],
      ['label' => 'Produits à faible stock','value' => $stats['low_stock_count'],    'icon' => 'bi-exclamation-triangle', 'color' => 'bg-warning bg-opacity-10 text-warning'],
    ]),
    ['label' => 'Clients',         'value' => $p['customers_count'], 'icon' => 'bi-people',      'color' => 'bg-primary bg-opacity-10 text-primary'],
    ['label' => 'Nouveaux clients','value' => $p['new_customers'],   'icon' => 'bi-person-plus', 'color' => 'bg-info bg-opacity-10 text-info'],
    ...($isCashier ? [] : [
      ['label' => 'Nombre d\'échanges', 'value' => $p['exchanges_count'], 'icon' => 'bi-arrow-left-right', 'color' => 'bg-warning bg-opacity-10 text-warning'],
    ]),
    ['label' => 'Garanties actives',  'value' => $p['warranties_active_count'],  'icon' => 'bi-shield-check', 'color' => 'bg-success bg-opacity-10 text-success'],
    ['label' => 'Garanties expirées', 'value' => $p['warranties_expired_count'], 'icon' => 'bi-shield-x',     'color' => 'bg-secondary bg-opacity-10 text-secondary'],
  ];
@endphp

<div class="row g-3">
  @foreach($kpis as $kpi)
    <div class="col-6 col-md-4 col-xl-3">
      <div class="kpi-card">
        <div class="d-flex align-items-center gap-3">
          <div class="kpi-icon {{ $kpi['color'] }}">
            <i class="bi {{ $kpi['icon'] }}"></i>
          </div>
          <div>
            <div class="kpi-label">{{ $kpi['label'] }}</div>
            <div class="kpi-value">{{ $kpi['value'] }}</div>
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

<div class="text-end mb-2">
  <a href="{{ route('reports.index') }}" class="small text-decoration-none">
    Voir le rapport complet <i class="bi bi-arrow-right"></i>
  </a>
</div>
