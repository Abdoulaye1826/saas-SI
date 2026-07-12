{{-- Filtre de période, intégré en overlay sur le KPI principal (voir
     dashboard/index.blade.php pour le positionnement et
     dashboard/partials/kpis.blade.php pour le libellé de période affiché
     dans la carte). Rendu côté serveur avec l'état courant (fonctionne dès
     le premier chargement, sans JS) ; le JS de index.blade.php prend
     ensuite le relais pour un rafraîchissement AJAX sans rechargement de
     page. Ce bloc n'est jamais remplacé par l'AJAX (contrairement aux KPI
     eux-mêmes) : ses écouteurs d'événements restent donc valides après
     chaque changement de période. --}}
<div class="hero-period-control">
  <select id="periodSelect" class="hero-period-select">
    <option value="today" @selected($period->key === 'today')>Aujourd'hui</option>
    <option value="yesterday" @selected($period->key === 'yesterday')>Hier</option>
    <option value="week" @selected($period->key === 'week')>Cette semaine</option>
    <option value="month" @selected($period->key === 'month')>Ce mois</option>
    <option value="year" @selected($period->key === 'year')>Cette année</option>
    <option value="custom" @selected($period->key === 'custom')>Période personnalisée</option>
  </select>

  <div id="customPeriodFields" class="hero-period-popover @if($period->key !== 'custom') d-none @endif">
    <div class="filter-field filter-field-date mb-2">
      <label for="periodStart" class="form-label small mb-1">Date de début</label>
      <input type="date" id="periodStart" class="form-control form-control-sm" value="{{ $period->key === 'custom' ? $period->start->toDateString() : '' }}">
    </div>
    <div class="filter-field filter-field-date mb-2">
      <label for="periodEnd" class="form-label small mb-1">Date de fin</label>
      <input type="date" id="periodEnd" class="form-control form-control-sm" value="{{ $period->key === 'custom' ? $period->end->toDateString() : '' }}">
    </div>
    <button type="button" id="applyCustomPeriod" class="btn btn-primary btn-sm w-100">
      <i class="bi bi-check-lg me-1"></i>Appliquer
    </button>
  </div>
</div>
