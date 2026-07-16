@extends('layouts.dashboard')

@section('title', 'Rapports financiers')
@section('page-title', 'Rapports financiers')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-file-earmark-bar-graph me-2"></i>Rapports financiers</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item active">Rapports financiers</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm filter-card mb-4">
  <div class="card-body">
    <form method="GET" id="periodForm" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Période</label>
        <select name="period" class="form-select">
          <option value="today" @selected($period->key === 'today')>Aujourd'hui</option>
          <option value="yesterday" @selected($period->key === 'yesterday')>Hier</option>
          <option value="week" @selected($period->key === 'week')>Cette semaine</option>
          <option value="month" @selected($period->key === 'month')>Ce mois</option>
          <option value="year" @selected($period->key === 'year')>Cette année</option>
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Appliquer la période</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3">
  @foreach($types as $slug => $label)
    <div class="col-md-6 col-lg-4">
      <div class="dashboard-summary-card d-flex flex-column h-100">
        <div class="summary-card-top">
          <i class="bi bi-file-earmark-text summary-icon text-primary"></i>
        </div>
        <div class="fw-semibold mb-3">{{ $label }}</div>
        <div class="mt-auto d-flex gap-2">
          <a href="{{ route('finance.reports.pdf', array_merge(['type' => $slug], $period->toQueryParams())) }}" class="btn btn-sm btn-outline-secondary flex-fill">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
          </a>
          <a href="{{ route('finance.reports.export', array_merge(['type' => $slug], $period->toQueryParams())) }}" class="btn btn-sm btn-outline-secondary flex-fill">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
          </a>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
