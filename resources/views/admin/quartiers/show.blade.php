@extends('admin.layouts.app')

@section('title', 'Détails du Quartier - ' . $quartier->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $quartier->nom }}</h1>
                    <p class="text-muted mb-0">{{ $quartier->ville }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.quartiers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('admin.quartiers.edit', $quartier) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('admin.quartiers.export-clients', $quartier) }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Exporter
                    </a>
                </div>
            </div>

            <!-- Statistiques du quartier -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['total_clients'] }}</h4>
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
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['clients_actifs'] }}</h4>
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
                                    <h4 class="card-title">{{ $stats['clients_en_attente'] }}</h4>
                                    <p class="card-text">En Attente</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['clients_suspendus'] }}</h4>
                                    <p class="card-text">Suspendus</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Informations du quartier -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations du Quartier</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $quartier->nom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ville :</strong></td>
                                    <td>{{ $quartier->ville }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        @if($quartier->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-danger">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($quartier->description)
                                    <tr>
                                        <td><strong>Description :</strong></td>
                                        <td>{{ $quartier->description }}</td>
                                    </tr>
                                @endif
                                @if($quartier->hasCoordinates())
                                    <tr>
                                        <td><strong>Coordonnées :</strong></td>
                                        <td>
                                            {{ $quartier->latitude }}, {{ $quartier->longitude }}
                                            <br><small class="text-muted">Latitude, Longitude</small>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Créé le :</strong></td>
                                    <td>{{ $quartier->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($stats['derniere_inscription'])
                                    <tr>
                                        <td><strong>Dernière inscription :</strong></td>
                                        <td>{{ $stats['derniere_inscription']->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Clients récents -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Clients Récents</h5>
                            <a href="{{ route('admin.quartiers.clients', $quartier) }}" class="btn btn-sm btn-primary">
                                Voir tous les clients
                            </a>
                        </div>
                        <div class="card-body">
                            @if($recentClients->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Statut</th>
                                                <th>Inscription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentClients as $client)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $client->full_name }}</strong>
                                                    </td>
                                                    <td>{{ $client->email }}</td>
                                                    <td>{{ $client->numero_telephone }}</td>
                                                    <td>
                                                        @switch($client->status)
                                                            @case('active')
                                                                <span class="badge badge-success">Actif</span>
                                                                @break
                                                            @case('pending')
                                                                <span class="badge badge-warning">En attente</span>
                                                                @break
                                                            @case('suspended')
                                                                <span class="badge badge-danger">Suspendu</span>
                                                                @break
                                                            @default
                                                                <span class="badge badge-secondary">{{ $client->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.users.show', $client) }}"
                                                           class="btn btn-sm btn-info" title="Voir profil">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun client dans ce quartier</h5>
                                    <p class="text-muted">Les clients qui s'inscrivent dans ce quartier apparaîtront ici.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






















