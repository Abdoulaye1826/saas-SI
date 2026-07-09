{{-- Filtre de période du tableau de bord. Rendu côté serveur avec l'état
     courant (fonctionne dès le premier chargement, sans JS) ; le JS de
     dashboard/index.blade.php prend ensuite le relais pour un rafraîchissement
     AJAX sans rechargement de page. --}}
<div class="filter-card mb-4">
  <div class="card-body d-flex flex-column flex-md-row flex-wrap align-items-md-end gap-3">
    <div class="filter-field filter-field-period">
      <label for="periodSelect" class="form-label small mb-1">Période</label>
      <select id="periodSelect" class="form-select">
        <option value="today" @selected($period->key === 'today')>Aujourd'hui</option>
        <option value="yesterday" @selected($period->key === 'yesterday')>Hier</option>
        <option value="week" @selected($period->key === 'week')>Cette semaine</option>
        <option value="month" @selected($period->key === 'month')>Ce mois</option>
        <option value="year" @selected($period->key === 'year')>Cette année</option>
        <option value="custom" @selected($period->key === 'custom')>Période personnalisée</option>
      </select>
    </div>

    <div id="customPeriodFields" class="d-flex flex-column flex-sm-row flex-wrap align-items-sm-end gap-2 filter-field-custom @if($period->key !== 'custom') d-none @endif">
      <div class="filter-field filter-field-date">
        <label for="periodStart" class="form-label small mb-1">Date de début</label>
        <input type="date" id="periodStart" class="form-control" value="{{ $period->key === 'custom' ? $period->start->toDateString() : '' }}">
      </div>
      <div class="filter-field filter-field-date">
        <label for="periodEnd" class="form-label small mb-1">Date de fin</label>
        <input type="date" id="periodEnd" class="form-control" value="{{ $period->key === 'custom' ? $period->end->toDateString() : '' }}">
      </div>
      <button type="button" id="applyCustomPeriod" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>Appliquer
      </button>
    </div>

    <div class="text-muted small filter-period-label" id="periodLabel">
      <i class="bi bi-calendar3 me-1"></i><span>{{ $period->label }}</span>
    </div>
  </div>
</div>
