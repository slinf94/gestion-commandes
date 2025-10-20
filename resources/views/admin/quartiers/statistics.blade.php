@extends('admin.layouts.app')

@section('title', 'Statistiques par Quartier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Statistiques par Quartier</h1>
                <a href="{{ route('admin.quartiers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <!-- Vue d'ensemble -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $quartiers->count() }}</h4>
                                    <p class="card-text">Quartiers Actifs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $quartiers->sum('clients_count') }}</h4>
                                    <p class="card-text">Total Clients</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $quartiers->sum('active_clients_count') }}</h4>
                                    <p class="card-text">Clients Actifs</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $quartiers->sum('clients_count') > 0 ? round($quartiers->sum('active_clients_count') / $quartiers->sum('clients_count') * 100, 1) : 0 }}%</h4>
                                    <p class="card-text">Taux d'Activité</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-percentage fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des statistiques -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistiques Détaillées par Quartier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Quartier</th>
                                    <th>Ville</th>
                                    <th>Total Clients</th>
                                    <th>Clients Actifs</th>
                                    <th>Taux d'Activité</th>
                                    <th>Inscriptions (12 derniers mois)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quartiers as $quartier)
                                    <tr>
                                        <td>
                                            <strong>{{ $quartier->nom }}</strong>
                                            @if($quartier->description)
                                                <br><small class="text-muted">{{ Str::limit($quartier->description, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $quartier->ville }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $quartier->clients_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $quartier->active_clients_count }}</span>
                                        </td>
                                        <td>
                                            @if($quartier->clients_count > 0)
                                                @php
                                                    $taux = round($quartier->active_clients_count / $quartier->clients_count * 100, 1);
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar
                                                        @if($taux >= 80) bg-success
                                                        @elseif($taux >= 60) bg-info
                                                        @elseif($taux >= 40) bg-warning
                                                        @else bg-danger
                                                        @endif"
                                                         role="progressbar"
                                                         style="width: {{ $taux }}%"
                                                         aria-valuenow="{{ $taux }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $taux }}%
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($monthlyStats[$quartier->id]) && $monthlyStats[$quartier->id]->count() > 0)
                                                <div class="small">
                                                    @foreach($monthlyStats[$quartier->id]->take(6) as $stat)
                                                        <span class="badge badge-light mr-1">
                                                            {{ $stat->month }}/{{ $stat->year }}: {{ $stat->count }}
                                                        </span>
                                                    @endforeach
                                                    @if($monthlyStats[$quartier->id]->count() > 6)
                                                        <small class="text-muted">+{{ $monthlyStats[$quartier->id]->count() - 6 }} autres</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Aucune inscription</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.quartiers.show', $quartier) }}"
                                                   class="btn btn-sm btn-info" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.quartiers.clients', $quartier) }}"
                                                   class="btn btn-sm btn-primary" title="Voir clients">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                <a href="{{ route('admin.quartiers.export-clients', $quartier) }}"
                                                   class="btn btn-sm btn-success" title="Exporter">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Graphiques (si vous voulez ajouter des graphiques) -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Répartition des Clients par Quartier</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="clientsChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top 5 des Quartiers</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="topQuartiersChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de répartition des clients
const clientsCtx = document.getElementById('clientsChart').getContext('2d');
const clientsChart = new Chart(clientsCtx, {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($quartiers as $quartier)
                '{{ $quartier->nom }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($quartiers as $quartier)
                    {{ $quartier->clients_count }},
                @endforeach
            ],
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Graphique des top 5 quartiers
const topQuartiers = @json($quartiers->sortByDesc('clients_count')->take(5));
const topQuartiersCtx = document.getElementById('topQuartiersChart').getContext('2d');
const topQuartiersChart = new Chart(topQuartiersCtx, {
    type: 'bar',
    data: {
        labels: topQuartiers.map(q => q.nom),
        datasets: [{
            label: 'Nombre de clients',
            data: topQuartiers.map(q => q.clients_count),
            backgroundColor: '#36A2EB'
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
@endpush
@endsection






















