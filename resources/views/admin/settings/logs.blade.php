@extends('admin.layouts.app')

@section('title', 'Logs de l\'Application - Allo Mobile')
@section('page-title', 'Logs de l\'Application')

@section('content')
<!-- Header moderne avec gradient vert Allo Mobile -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
            <i class="fas fa-file-alt me-2"></i>Logs de l'Application
        </h3>
        <small class="opacity-75">Allo Mobile - Consultation des logs système</small>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
            <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                <i class="fas fa-filter me-2"></i>Filtres et Recherche
            </h5>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.settings.logs') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="level" class="form-label">Niveau de log</label>
                        <select name="level" id="level" class="form-select" style="border-radius: 8px;">
                            <option value="all" {{ $selectedLevel === 'all' ? 'selected' : '' }}>Tous les niveaux</option>
                            <option value="error" {{ $selectedLevel === 'error' ? 'selected' : '' }}>Erreurs</option>
                            <option value="warning" {{ $selectedLevel === 'warning' ? 'selected' : '' }}>Avertissements</option>
                            <option value="info" {{ $selectedLevel === 'info' ? 'selected' : '' }}>Informations</option>
                            <option value="debug" {{ $selectedLevel === 'debug' ? 'selected' : '' }}>Debug</option>
                            <option value="critical" {{ $selectedLevel === 'critical' ? 'selected' : '' }}>Critiques</option>
                            <option value="alert" {{ $selectedLevel === 'alert' ? 'selected' : '' }}>Alertes</option>
                            <option value="emergency" {{ $selectedLevel === 'emergency' ? 'selected' : '' }}>Urgences</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="lines" class="form-label">Nombre de lignes</label>
                        <select name="lines" id="lines" class="form-select" style="border-radius: 8px;">
                            <option value="50" {{ $lines == 50 ? 'selected' : '' }}>50 dernières lignes</option>
                            <option value="100" {{ $lines == 100 ? 'selected' : '' }}>100 dernières lignes</option>
                            <option value="200" {{ $lines == 200 ? 'selected' : '' }}>200 dernières lignes</option>
                            <option value="500" {{ $lines == 500 ? 'selected' : '' }}>500 dernières lignes</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Rechercher dans les logs..." 
                                   value="{{ $search }}"
                                   style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-filter me-2"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.settings.logs') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </a>
                            <a href="{{ route('admin.settings.system') }}" class="btn btn-outline-primary" style="border-radius: 8px;">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations sur le fichier -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #38B04A;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt fa-2x me-3" style="color: #38B04A;"></i>
                        <div>
                            <h6 class="text-muted mb-1">Fichier de log</h6>
                            <p class="mb-0" style="font-weight: 600; color: #495057;">laravel.log</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #0dcaf0;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-weight fa-2x me-3" style="color: #0dcaf0;"></i>
                        <div>
                            <h6 class="text-muted mb-1">Taille du fichier</h6>
                            <p class="mb-0" style="font-weight: 600; color: #495057;">{{ $fileSizeFormatted }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des logs -->
    <div class="card border-0 shadow-lg" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                    <i class="fas fa-list me-2"></i>Logs ({{ count($logs) }} résultat(s))
                </h5>
                <span class="badge" style="background: linear-gradient(135deg, #38B04A, #4CAF50); color: white; padding: 6px 12px; border-radius: 8px;">
                    Plus récents en premier
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if(count($logs) > 0)
                <div style="max-height: 600px; overflow-y: auto;">
                    @foreach($logs as $index => $log)
                        @php
                            $levelColors = [
                                'error' => ['bg' => '#dc3545', 'icon' => 'fa-exclamation-circle'],
                                'warning' => ['bg' => '#ffc107', 'icon' => 'fa-exclamation-triangle'],
                                'info' => ['bg' => '#0dcaf0', 'icon' => 'fa-info-circle'],
                                'debug' => ['bg' => '#6c757d', 'icon' => 'fa-bug'],
                                'critical' => ['bg' => '#dc3545', 'icon' => 'fa-times-circle'],
                                'alert' => ['bg' => '#ff9800', 'icon' => 'fa-bell'],
                                'emergency' => ['bg' => '#dc3545', 'icon' => 'fa-exclamation-triangle'],
                            ];
                            $levelInfo = $levelColors[$log['level']] ?? ['bg' => '#6c757d', 'icon' => 'fa-circle'];
                        @endphp
                        <div class="log-entry" style="border-bottom: 1px solid #e9ecef; padding: 15px; transition: background-color 0.3s;" 
                             onmouseover="this.style.backgroundColor='#f8f9fa'" 
                             onmouseout="this.style.backgroundColor='white'">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background: {{ $levelInfo['bg'] }}; color: white;">
                                        <i class="fas {{ $levelInfo['icon'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge" style="background: {{ $levelInfo['bg'] }}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                                {{ strtoupper($log['level']) }}
                                            </span>
                                            <span class="badge bg-secondary ms-2" style="padding: 4px 10px; border-radius: 6px; font-size: 0.75rem;">
                                                {{ $log['environment'] }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $log['timestamp'] }}
                                        </small>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 3px solid {{ $levelInfo['bg'] }}; font-family: 'Courier New', monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-word; color: #495057;">
{{ $log['message'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                    <h5 class="text-muted mb-2">Aucun log trouvé</h5>
                    <p class="text-muted">
                        @if(empty($search) && $selectedLevel === 'all')
                            Le fichier de log est vide ou n'existe pas encore.
                        @else
                            Aucun log ne correspond à vos critères de recherche.
                        @endif
                    </p>
                    <a href="{{ route('admin.settings.logs') }}" class="btn btn-success mt-3" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; border-radius: 8px;">
                        <i class="fas fa-redo me-2"></i>Réinitialiser les filtres
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.log-entry:last-child {
    border-bottom: none;
}

.gap-2 > * + * {
    margin-left: 0.5rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #38B04A;
    box-shadow: 0 0 0 0.2rem rgba(56, 176, 74, 0.25);
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #38B04A, #4CAF50);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #4CAF50, #38B04A);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit sur changement de sélection
    const levelSelect = document.getElementById('level');
    const linesSelect = document.getElementById('lines');
    
    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    if (linesSelect) {
        linesSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
});
</script>
@endsection


