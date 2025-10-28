@extends('admin.layouts.app')

@section('title', 'Gestion des Quartiers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gestion des Quartiers</h1>
                <div>
                    <a href="{{ route('admin.quartiers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Quartier
                    </a>
                    <a href="{{ route('admin.quartiers.statistics') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Statistiques
                    </a>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['total_quartiers'] }}</h4>
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
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">{{ $stats['clients_avec_quartier'] }}</h4>
                                    <p class="card-text">Avec Quartier</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                    <h4 class="card-title">{{ $stats['clients_sans_quartier'] }}</h4>
                                    <p class="card-text">Sans Quartier</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des quartiers -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des Quartiers</h5>
                </div>
                <div class="card-body">
                    @if($quartiers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Ville</th>
                                        <th>Total Clients</th>
                                        <th>Clients Actifs</th>
                                        <th>Dernière Inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quartiers as $quartier)
                                        <tr>
                                            <td>
                                                <strong>{{ $quartier->nom }}</strong>
                                                @if($quartier->description)
                                                    <br><small class="text-muted">{{ Str::limit($quartier->description, 50) }}</small>
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
                                                    {{ $quartier->clients->max('created_at') ? $quartier->clients->max('created_at')->format('d/m/Y') : 'N/A' }}
                                                @else
                                                    <span class="text-muted">Aucun client</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1" style="min-width: 150px;">
                                                    <a href="{{ route('admin.quartiers.show', $quartier) }}"
                                                       class="btn btn-sm btn-info" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.quartiers.clients', $quartier) }}"
                                                       class="btn btn-sm btn-primary" title="Voir clients">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="{{ route('admin.quartiers.edit', $quartier) }}"
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.quartiers.export-clients', $quartier) }}"
                                                       class="btn btn-sm btn-success" title="Exporter clients">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($quartier->clients_count == 0)
                                                        <form action="{{ route('admin.quartiers.destroy', $quartier) }}"
                                                              id="delete-quartier-{{ $quartier->id }}"
                                                              method="POST" class="d-inline delete-quartier-form">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <button type="button" class="btn btn-sm btn-danger delete-quartier-btn" title="Supprimer"
                                                                data-form-id="delete-quartier-{{ $quartier->id }}"
                                                                data-quartier-name="{{ $quartier->name }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun quartier trouvé</h5>
                            <p class="text-muted">Commencez par créer votre premier quartier.</p>
                            <a href="{{ route('admin.quartiers.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Créer un quartier
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer les boutons de suppression de quartiers
    const deleteButtons = document.querySelectorAll('.delete-quartier-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-form-id');
            const quartierName = this.getAttribute('data-quartier-name');
            const form = document.getElementById(formId);

            if (!form) return;

            customConfirm(
                `Êtes-vous sûr de vouloir supprimer le quartier <strong>"${quartierName}"</strong> ? Cette action est irréversible.`,
                function() {
                    form.submit();
                },
                null,
                'Suppression de quartier',
                'Oui, supprimer',
                'Annuler'
            );
        });
    });
});
</script>
@endpush
@endsection








