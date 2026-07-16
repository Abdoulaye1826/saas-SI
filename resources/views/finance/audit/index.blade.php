@extends('layouts.dashboard')

@section('title', 'Audit financier')
@section('page-title', 'Audit financier')

@section('content')
<div class="page-header">
  <h1><i class="bi bi-shield-check me-2"></i>Audit financier</h1>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
      <li class="breadcrumb-item active">Audit financier</li>
    </ol>
  </nav>
</div>

<div class="card border-0 shadow-sm filter-card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label small">Action</label>
        <select name="action" class="form-select">
          <option value="">Toutes</option>
          <option value="create" @selected(request('action') === 'create')>Créations</option>
          <option value="create_auto" @selected(request('action') === 'create_auto')>Créations automatiques</option>
          <option value="update" @selected(request('action') === 'update')>Modifications</option>
          <option value="delete" @selected(request('action') === 'delete')>Suppressions</option>
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filtrer</button>
      </div>
    </form>
  </div>
</div>

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Utilisateur</th>
          <th>Action</th>
          <th>Description</th>
          <th>Modification</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->created_at->format('d/m/Y') }}</td>
            <td>{{ $log->created_at->format('H:i:s') }}</td>
            <td>{{ $log->user->name ?? 'Système' }}</td>
            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
            <td>{{ $log->description }}</td>
            <td>
              @if($log->old_values || $log->new_values)
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#diffModal{{ $log->id }}">
                  <i class="bi bi-eye"></i> Voir
                </button>
                <div class="modal fade" id="diffModal{{ $log->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Détail de la modification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-6">
                            <h6 class="text-muted small text-uppercase">Ancienne valeur</h6>
                            <pre class="small bg-light p-2 rounded">{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
                          </div>
                          <div class="col-6">
                            <h6 class="text-muted small text-uppercase">Nouvelle valeur</h6>
                            <pre class="small bg-light p-2 rounded">{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">Aucune activité enregistrée.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-3 border-top">{{ $logs->links() }}</div>
</div>
@endsection
