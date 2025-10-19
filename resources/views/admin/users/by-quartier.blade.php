@extends('admin.layouts.app')

@section('title', 'Clients par Quartier - Allo Mobile Admin')
@section('page-title', 'Clients par Quartier')

@section('styles')
<style>
    .quartier-card { border-left: 4px solid #4CAF50; transition: all 0.3s; }
    .quartier-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour à la liste
                </a>
            </div>
            <div>
                <a href="{{ route('admin.users.export.by-quartier.csv') }}" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>
                    Exporter (CSV)
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ $totalClients }}</h4>
                <p class="mb-0">Total Clients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ $quartiersAvecClients }}</h4>
                <p class="mb-0">Quartiers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h4>{{ $activeClients }}</h4>
                <p class="mb-0">Clients Actifs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $pendingClients }}</h4>
                <p class="mb-0">Clients en Attente</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des quartiers -->
<div class="row">
    @forelse($quartiersStats as $quartierData)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card quartier-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $quartierData['quartier'] }}
                </h5>
                <span class="badge bg-primary">{{ $quartierData['total_clients'] }} clients</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Pourcentage du total :</small>
                    <div class="progress mt-1">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $quartierData['total_clients'] > 0 ? ($quartierData['total_clients'] / $totalClients) * 100 : 0 }}%"
                             aria-valuenow="{{ $quartierData['total_clients'] }}"
                             aria-valuemin="0"
                             aria-valuemax="{{ $totalClients }}">
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $quartierData['total_clients'] > 0 ? number_format(($quartierData['total_clients'] / $totalClients) * 100, 1) : 0 }}%
                    </small>
                </div>

                <div class="mb-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="text-success mb-0">{{ $quartierData['active_clients'] }}</h6>
                                <small class="text-muted">Actifs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-warning mb-0">{{ $quartierData['total_clients'] - $quartierData['active_clients'] }}</h6>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index', ['quartier' => $quartierData['quartier']]) }}"
                   class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>
                    Voir les clients
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun quartier trouvé</h5>
            <p class="text-muted">Les quartiers apparaîtront ici une fois que des clients y seront enregistrés.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Graphique des quartiers (si disponible) -->
@if(isset($quartierChart) && $quartierChart)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Répartition par Quartier
                </h5>
            </div>
            <div class="card-body">
                <canvas id="quartierChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if(isset($quartierChart) && $quartierChart)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('quartierChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($quartierChart['labels']) !!},
            datasets: [{
                label: 'Nombre de clients',
                data: {!! json_encode($quartierChart['data']) !!},
                backgroundColor: '#4CAF50',
                borderColor: '#2E7D32',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
@endif
