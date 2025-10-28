@extends('admin.layouts.app')

@section('title', 'Journal des Activités - Allo Mobile Admin')
@section('page-title', 'Journal des Activités')

@section('styles')
<style>
    .activity-card {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    .activity-card:hover {
        transform: translateY(-2px);
    }
    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .activity-badge {
        font-size: 0.8em;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    .activity-created { background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; }
    .activity-updated { background: linear-gradient(135deg, #2196F3, #1976D2); color: white; }
    .activity-deleted { background: linear-gradient(135deg, #f44336, #d32f2f); color: white; }
    .activity-restored { background: linear-gradient(135deg, #FF9800, #F57C00); color: white; }
    .activity-logged_in { background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: white; }
    .activity-logged_out { background: linear-gradient(135deg, #607D8B, #455A64); color: white; }
    .activity-other { background: linear-gradient(135deg, #6C757D, #495057); color: white; }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .stats-card.success { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
    .stats-card.warning { background: linear-gradient(135deg, #FF9800, #F57C00); }
    .stats-card.info { background: linear-gradient(135deg, #2196F3, #1976D2); }
    .stats-card.danger { background: linear-gradient(135deg, #f44336, #d32f2f); }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9em;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }

    .activity-details {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Journal des Activités</h4>
        <small class="text-muted">Suivi de toutes les actions des utilisateurs</small>
    </div>
    <div>
        <button class="btn btn-outline-primary me-2" onclick="refreshLogs()">
            <i class="fas fa-sync-alt me-2"></i>Actualiser
        </button>
        <a href="{{ route('admin.activity-logs.export') }}" class="btn btn-success me-2">
            <i class="fas fa-download me-2"></i>Exporter CSV
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-list fa-2x mb-2"></i>
            <h4 class="mb-0" id="total-activities">-</h4>
            <p class="mb-0">Total Activités</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success text-center">
            <i class="fas fa-calendar-day fa-2x mb-2"></i>
            <h4 class="mb-0" id="today-activities">-</h4>
            <p class="mb-0">Aujourd'hui</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info text-center">
            <i class="fas fa-calendar-week fa-2x mb-2"></i>
            <h4 class="mb-0" id="week-activities">-</h4>
            <p class="mb-0">Cette Semaine</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning text-center">
            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
            <h4 class="mb-0" id="month-activities">-</h4>
            <p class="mb-0">Ce Mois</p>
        </div>
    </div>
</div>

<!-- Section de filtres -->
<div class="filter-section">
    <form id="filterForm" method="GET" action="{{ route('admin.activity-logs.index') }}">
        <div class="row align-items-end">
            <div class="col-md-2 mb-3">
                <label for="user_id" class="form-label">Utilisateur</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->nom }} {{ $user->prenom }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="subject_type" class="form-label">Modèle</label>
                <select name="subject_type" id="subject_type" class="form-select">
                    <option value="">Tous les modèles</option>
                    @foreach($subjectTypes as $type)
                        <option value="{{ $type }}" {{ request('subject_type') == $type ? 'selected' : '' }}>
                            {{ class_basename($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="log_name" class="form-label">Type de Log</label>
                <select name="log_name" id="log_name" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach($logNames as $logName)
                        <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                            {{ $logName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="activity_type" class="form-label">Action</label>
                <select name="activity_type" id="activity_type" class="form-select">
                    <option value="">Toutes les actions</option>
                    @foreach($activityTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('activity_type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="date_from" class="form-label">Date début</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2 mb-3">
                <label for="date_to" class="form-label">Date fin</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 mb-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Rechercher dans les descriptions, utilisateurs..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 mb-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Loading spinner -->
<div class="loading-spinner" id="loadingSpinner">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
    </div>
</div>

<!-- Table des activités -->
<div class="card activity-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Activités Récentes</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Modèle</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="activitiesTableBody">
                    @include('admin.activity_logs.partials.activities_table', ['activities' => $activities])
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer">
    @include('admin.activity_logs.partials.pagination', ['activities' => $activities])
</div>

@endsection

@section('scripts')
<script>
    // Charger les statistiques au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
    });

    // Charger les statistiques
    function loadStatistics() {
        fetch('{{ route("admin.activity-logs.statistics") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-activities').textContent = data.stats.total_activities;
                document.getElementById('today-activities').textContent = data.stats.today_activities;
                document.getElementById('week-activities').textContent = data.stats.this_week_activities;
                document.getElementById('month-activities').textContent = data.stats.this_month_activities;
            })
            .catch(error => console.error('Erreur lors du chargement des statistiques:', error));
    }

    // Actualiser les logs
    function refreshLogs() {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const tableBody = document.getElementById('activitiesTableBody');
        const paginationContainer = document.getElementById('paginationContainer');

        loadingSpinner.style.display = 'block';

        // Construire l'URL avec les paramètres actuels
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);

        fetch(`{{ route('admin.activity-logs.get-logs') }}?${params}`)
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination;
                loadingSpinner.style.display = 'none';

                // Recharger les statistiques
                loadStatistics();
            })
            .catch(error => {
                console.error('Erreur lors du rechargement:', error);
                loadingSpinner.style.display = 'none';
            });
    }

    // Auto-submit du formulaire de filtre
    document.getElementById('filterForm').addEventListener('change', function() {
        this.submit();
    });

    // Recherche en temps réel
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
</script>
@endsection


