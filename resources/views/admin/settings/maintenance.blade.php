@extends('admin.layouts.app')

@section('title', 'Maintenance - Allo Mobile')
@section('page-title', 'Maintenance')

@section('content')
<!-- Header moderne avec gradient vert Allo Mobile -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
            <i class="fas fa-tools me-2"></i>Maintenance du Système
        </h3>
        <small class="opacity-75">Allo Mobile - Outils de maintenance et optimisation</small>
    </div>
</div>

<div class="container-fluid">
    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #38B04A;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-database fa-2x" style="color: #38B04A;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Base de Données</h6>
                            @if(isset($dbStats['error']))
                                <p class="mb-0 text-danger" style="font-weight: 600;">Erreur</p>
                            @else
                                <p class="mb-0" style="font-weight: 600; color: #495057;">
                                    {{ $dbStats['total_tables'] ?? 0 }} table(s)
                                    @if(isset($dbStats['total_size']))
                                        <br><small class="text-muted">{{ number_format($dbStats['total_size'], 2) }} MB</small>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-file-alt fa-2x" style="color: #ffc107;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Fichiers de Log</h6>
                            <p class="mb-0" style="font-weight: 600; color: #495057;">
                                @php
                                    $logSizeFormatted = $logSize > 0 ? number_format($logSize / 1024 / 1024, 2) . ' MB' : '0 MB';
                                @endphp
                                {{ $logSizeFormatted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #0dcaf0;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <i class="fas fa-bolt fa-2x" style="color: #0dcaf0;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Cache</h6>
                            <p class="mb-0" style="font-weight: 600; color: #495057;">
                                @php
                                    $cacheSizeFormatted = $cacheSize > 0 ? number_format($cacheSize / 1024 / 1024, 2) . ' MB' : '0 MB';
                                @endphp
                                {{ $cacheSizeFormatted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions de Maintenance -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-cog me-2"></i>Actions de Maintenance
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <div class="row g-3">
                        <!-- Vider le Cache -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #ffc107 !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-trash fa-3x mb-3" style="color: #ffc107;"></i>
                                    <h6 class="mb-2">Vider le Cache</h6>
                                    <p class="text-muted small mb-3">Nettoie tous les fichiers de cache de l'application</p>
                                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" onsubmit="return false;">
                                        @csrf
                                        <button type="button" class="btn btn-warning w-100"
                                                onclick="submitWithConfirmation(this.closest('form'), 'Êtes-vous sûr de vouloir vider le cache ? Cette action va nettoyer tous les fichiers de cache de l\\'application.')">
                                            <i class="fas fa-trash me-2"></i>Vider le Cache
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Optimiser la Base de Données -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #38B04A !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-database fa-3x mb-3" style="color: #38B04A;"></i>
                                    <h6 class="mb-2">Optimiser la Base de Données</h6>
                                    <p class="text-muted small mb-3">Optimise toutes les tables pour améliorer les performances</p>
                                    <form action="{{ route('admin.settings.optimize-db') }}" method="POST" onsubmit="return false;">
                                        @csrf
                                        <button type="button" class="btn btn-success w-100"
                                                onclick="submitWithConfirmation(this.closest('form'), 'Êtes-vous sûr de vouloir optimiser la base de données ? Cette action va optimiser toutes les tables pour améliorer les performances.')">
                                            <i class="fas fa-database me-2"></i>Optimiser DB
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Vider les Logs -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #dc3545 !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x mb-3" style="color: #dc3545;"></i>
                                    <h6 class="mb-2">Vider les Logs</h6>
                                    <p class="text-muted small mb-3">Supprime tous les fichiers de log</p>
                                    <form action="{{ route('admin.settings.clear-logs') }}" method="POST" onsubmit="return false;">
                                        @csrf
                                        <button type="button" class="btn btn-danger w-100"
                                                onclick="submitWithConfirmation(this.closest('form'), 'Êtes-vous sûr de vouloir vider les logs ? Cette action est irréversible et supprimera tous les fichiers de log.')">
                                            <i class="fas fa-trash-alt me-2"></i>Vider les Logs
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Voir les Logs -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #6c757d !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-3x mb-3" style="color: #6c757d;"></i>
                                    <h6 class="mb-2">Voir les Logs</h6>
                                    <p class="text-muted small mb-3">Consulter les logs de l'application</p>
                                    <a href="{{ route('admin.settings.logs') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-eye me-2"></i>Voir les Logs
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Informations Système -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #0dcaf0 !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-server fa-3x mb-3" style="color: #0dcaf0;"></i>
                                    <h6 class="mb-2">Informations Système</h6>
                                    <p class="text-muted small mb-3">Consulter les informations du serveur</p>
                                    <a href="{{ route('admin.settings.system') }}" class="btn btn-info w-100">
                                        <i class="fas fa-server me-2"></i>Informations Système
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Actualiser -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border" style="border-radius: 12px; border-color: #0d6efd !important;">
                                <div class="card-body text-center">
                                    <i class="fas fa-sync fa-3x mb-3" style="color: #0d6efd;"></i>
                                    <h6 class="mb-2">Actualiser la Page</h6>
                                    <p class="text-muted small mb-3">Recharger la page pour voir les statistiques mises à jour</p>
                                    <button type="button" class="btn btn-primary w-100" onclick="location.reload()">
                                        <i class="fas fa-sync me-2"></i>Actualiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails Base de Données -->
    @if(isset($dbStats['tables']) && count($dbStats['tables']) > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-table me-2"></i>Détails des Tables de la Base de Données
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="padding: 15px; font-weight: 600; border-bottom: 2px solid #38B04A;">Table</th>
                                    <th style="padding: 15px; font-weight: 600; border-bottom: 2px solid #38B04A; text-align: right;">Taille (MB)</th>
                                    <th style="padding: 15px; font-weight: 600; border-bottom: 2px solid #38B04A; text-align: right;">Lignes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dbStats['tables'] as $table)
                                <tr>
                                    <td style="padding: 12px 15px;">
                                        <i class="fas fa-table me-2 text-muted"></i>
                                        <strong>{{ $table['name'] }}</strong>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right;">
                                        <span class="badge bg-info">{{ number_format($table['size'], 2) }} MB</span>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: right;">
                                        <span class="badge bg-secondary">{{ number_format($table['rows']) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td style="padding: 15px; font-weight: 600;">
                                        <strong>Total</strong>
                                    </td>
                                    <td style="padding: 15px; text-align: right; font-weight: 600;">
                                        <span class="badge" style="background: linear-gradient(135deg, #38B04A, #4CAF50); color: white;">
                                            {{ number_format($dbStats['total_size'], 2) }} MB
                                        </span>
                                    </td>
                                    <td style="padding: 15px; text-align: right;">
                                        <span class="badge bg-dark">-</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Bouton Retour -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-2"></i>Retour aux Paramètres
            </a>
        </div>
    </div>
</div>

<style>
.gap-3 > * + * {
    margin-left: 1rem;
}

@media (max-width: 768px) {
    .gap-3 > * + * {
        margin-left: 0;
        margin-top: 1rem;
    }
}
</style>
@endsection








