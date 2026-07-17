{{-- Tableau de bord simplifié à l'essentiel : un seul KPI principal (chiffre
     d'affaires) mis en avant, et deux KPI secondaires (nombre de ventes,
     montant encaissé). Le détail complet (facturation, panier moyen, marge,
     devis, valeur du stock, alertes...) reste disponible plus bas sur cette
     page (tableaux) et sur la page Rapports. Rechargée entièrement
     (innerHTML) à chaque changement de période. --}}
@php
  $p = $stats['period'];
@endphp

<div class="row g-3 mb-3">
  <div class="col-12">
    <div class="kpi-card kpi-card--hero">
      <div class="kpi-hero__icon"><i class="bi bi-currency-exchange"></i></div>
      <div>
        <div class="kpi-hero__label">
          Chiffre d'affaires
          <span class="kpi-hero__period" id="periodLabel"><i class="bi bi-calendar3"></i> <span>{{ $period->label }}</span></span>
        </div>
        <div class="kpi-hero__value">{{ number_format($p['revenue'], 0, ',', ' ') }} <span>FCFA</span></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-6">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-danger text-danger">
          <i class="bi bi-arrow-up-circle"></i>
        </div>
        <div>
          <div class="kpi-label">Dépenses</div>
          <div class="kpi-value">{{ number_format($p['depenses'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6">
    <div class="kpi-card">
      <div class="d-flex align-items-center gap-3">
        <div class="kpi-icon bg-success text-success">
          <i class="bi bi-cash-stack"></i>
        </div>
        <div>
          <div class="kpi-label">Solde net</div>
          <div class="kpi-value">{{ number_format($p['solde_net'], 0, ',', ' ') }} FCFA</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="text-end mb-2">
  <a href="{{ route('reports.index') }}" class="small text-decoration-none">
    Voir le rapport complet <i class="bi bi-arrow-right"></i>
  </a>
</div>
