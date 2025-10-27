@extends('admin.layouts.app')

@section('title', 'Gestion des Utilisateurs - Allo Mobile Admin')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<!-- Statistiques principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $users->total() }}</h4>
                        <p class="card-text">Total Utilisateurs</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $users->where('status', 'active')->count() }}</h4>
                        <p class="card-text">Utilisateurs Actifs</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $users->where('status', 'pending')->count() }}</h4>
                        <p class="card-text">En Attente</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $users->where('role', 'client')->count() }}</h4>
                        <p class="card-text">Clients</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-friends fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Nouvel Utilisateur
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.users.by-quartier') }}" class="btn btn-success w-100">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Par Quartier
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="dropdown">
                            <button class="btn btn-warning w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>
                                Exporter
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.export.csv', request()->query()) }}">
                                        <i class="fas fa-file-csv me-2"></i>Liste des clients (CSV)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.export.by-quartier.csv') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Statistiques par quartier (CSV)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-info w-100" onclick="toggleFilters()">
                            <i class="fas fa-filter me-2"></i>
                            Filtres
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres (masqués par défaut) -->
<div class="row mb-4" id="filters-section" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtres et Recherche
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quartier" class="form-label">Quartier</label>
                            <select class="form-select" id="quartier" name="quartier">
                                <option value="">Tous les quartiers</option>
                                @foreach($quartiers as $quartier)
                                    <option value="{{ $quartier }}" {{ request('quartier') == $quartier ? 'selected' : '' }}>
                                        {{ $quartier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Tous les rôles</option>
                                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="gestionnaire" {{ request('role') == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Nom, email, téléphone..." onkeyup="searchUsers()">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="per_page" class="form-label">Par page</label>
                            <select class="form-select" id="per_page" name="per_page">
                                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Liste des utilisateurs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>
                    Liste des Utilisateurs
                </h5>
            </div>
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Quartier</th>
                                    <th>Statut</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($user->nom, 0, 1)) }}{{ strtoupper(substr($user->prenom, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->nom }} {{ $user->prenom }}</strong>
                                                @if($user->role == 'admin')
                                                    <span class="badge bg-danger ms-1">Admin</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->numero_telephone ?? 'N/A' }}</td>
                                    <td>{{ $user->quartier ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusTranslations = [
                                                'active' => 'Actif',
                                                'inactive' => 'Inactif',
                                                'pending' => 'En attente',
                                                'suspended' => 'Suspendu'
                                            ];
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'pending' => 'warning',
                                                'suspended' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$user->status] ?? 'secondary' }} status-badge"
                                              data-user-id="{{ $user->id }}"
                                              data-current-status="{{ $user->status }}"
                                              style="cursor: {{ ($user->role !== 'admin' && $user->status !== 'pending') ? 'pointer' : 'default' }};"
                                              onclick="{{ ($user->role !== 'admin' && $user->status !== 'pending') ? 'toggleUserStatus(' . $user->id . ')' : '' }}">
                                            {{ $statusTranslations[$user->status] ?? ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->role === 'client' && $user->status === 'pending')
                                                <form method="POST" action="{{ route('admin.users.quick-activate', $user) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                            title="Activer rapidement"
                                                            onclick="return confirm('Activer le compte de {{ $user->nom }} {{ $user->prenom }} ?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($user->id != auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Supprimer"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                        <p class="text-muted">Commencez par créer un nouvel utilisateur.</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer un utilisateur
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }
</style>
@endsection

@section('scripts')
<script>
// Configuration des traductions et couleurs
const statusConfig = {
    translations: {
        'active': 'Actif',
        'inactive': 'Inactif',
        'pending': 'En attente',
        'suspended': 'Suspendu'
    },
    colors: {
        'active': 'success',
        'inactive': 'secondary',
        'pending': 'warning',
        'suspended': 'danger'
    }
};

// Fonction pour basculer le statut utilisateur
async function toggleUserStatus(userId) {
    const badge = document.querySelector(`[data-user-id="${userId}"]`);
    const currentStatus = badge.getAttribute('data-current-status');

    // Vérifier si l'utilisateur peut être modifié
    if (currentStatus === 'pending') {
        showAlert('Impossible de basculer le statut d\'un compte en attente. Utilisez l\'activation manuelle.', 'warning');
        return;
    }

    // Confirmation avant changement
    const newStatusText = currentStatus === 'active' ? 'Inactif' : 'Actif';
    if (!confirm(`Êtes-vous sûr de vouloir ${currentStatus === 'active' ? 'désactiver' : 'activer'} ce compte ?`)) {
        return;
    }

    // Désactiver le badge pendant la requête
    badge.style.opacity = '0.6';
    badge.style.pointerEvents = 'none';

    try {
        const response = await fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Mettre à jour le badge
            updateStatusBadge(badge, data.data.new_status, data.data.status_label, data.data.status_color);

            // Mettre à jour les statistiques en haut
            updateStatistics();

            showAlert(data.message, 'success');
        } else {
            showAlert(data.message || 'Erreur lors du changement de statut', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Erreur de connexion. Veuillez réessayer.', 'error');
    } finally {
        // Réactiver le badge
        badge.style.opacity = '1';
        badge.style.pointerEvents = 'auto';
    }
}

// Fonction pour mettre à jour le badge de statut
function updateStatusBadge(badge, newStatus, statusLabel, statusColor) {
    badge.setAttribute('data-current-status', newStatus);
    badge.textContent = statusLabel;
    badge.className = `badge bg-${statusColor} status-badge`;

    // Mettre à jour le style du curseur
    if (newStatus === 'pending') {
        badge.style.cursor = 'default';
        badge.removeAttribute('onclick');
    } else {
        badge.style.cursor = 'pointer';
        badge.setAttribute('onclick', `toggleUserStatus(${badge.getAttribute('data-user-id')})`);
    }
}

// Fonction pour mettre à jour les statistiques
function updateStatistics() {
    // Recharger la page pour mettre à jour les statistiques
    // Ou faire une requête AJAX pour mettre à jour seulement les cartes
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Fonction pour afficher les alertes
function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    // Insérer l'alerte en haut de la page
    const contentArea = document.querySelector('.main-content');
    contentArea.insertAdjacentHTML('afterbegin', alertHtml);

    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        const alert = contentArea.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}


function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
    } else {
        filtersSection.style.display = 'none';
    }
}

function searchUsers() {
    const searchInput = document.getElementById('search');
    const form = searchInput.closest('form');

    // Délai pour éviter trop de requêtes
    clearTimeout(searchUsers.timeout);
    searchUsers.timeout = setTimeout(() => {
        form.submit();
    }, 500);
}

// Filtrage automatique des sélecteurs
document.addEventListener('DOMContentLoaded', function() {
    const quartierSelect = document.getElementById('quartier');
    const roleSelect = document.getElementById('role');
    const statusSelect = document.getElementById('status');

    if (quartierSelect) {
        quartierSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
});
</script>
@endsection
