@extends('admin.layouts.app')

@section('title', 'Gestion des Utilisateurs - Allo Mobile Admin')
@section('page-title', 'Gestion des Utilisateurs')

@section('styles')
<style>
    /* Styles pour éviter l'overflow des boutons d'action */
    .actions-column { min-width: 120px; max-width: 150px; }
    .actions-container { display: flex; flex-wrap: wrap; gap: 2px; align-items: center; }
    .actions-container .btn { flex-shrink: 0; min-width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
    .actions-container .btn i { font-size: 0.8em; }

    /* Responsive pour les petits écrans */
    @media (max-width: 768px) {
        .actions-container { flex-direction: column; gap: 1px; }
        .actions-container .btn { width: 100%; min-width: 28px; height: 28px; }
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Exporter
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
            <div>
                <a href="{{ route('admin.users.by-quartier') }}" class="btn btn-info me-2">
                    <i class="fas fa-map-marker-alt"></i> Par Quartier
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nouvel Utilisateur
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

<!-- Filtres par quartier -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtres
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Nom, email, téléphone...">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
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
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary">{{ $users->total() }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ $users->where('status', 'active')->count() }}</h4>
                        <small class="text-muted">Actifs</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ $users->where('status', 'inactive')->count() }}</h4>
                        <small class="text-muted">Inactifs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des utilisateurs -->
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
                            <th class="actions-column">Actions</th>
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
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst(is_object($user->status) ? $user->status->value : $user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="actions-column">
                                <div class="actions-container">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
@endsection
