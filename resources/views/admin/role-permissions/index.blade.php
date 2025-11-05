@extends('admin.layouts.app')

@section('title', 'Gestion des Profils et Droits d\'Accès')
@section('page-title', 'Gestion des Profils et Droits d\'Accès')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Gestion des Profils et Droits d'Accès
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>
                    Gérez les rôles et permissions des utilisateurs administrateurs. Seul le Super Administrateur peut accéder à cette interface.
                </p>

                <!-- Formulaire de recherche et filtres -->
                <form method="GET" action="{{ route('admin.role-permissions.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Rechercher par nom, prénom ou email..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="role" class="form-select">
                                <option value="">Tous les rôles</option>
                                <option value="super-admin" {{ request('role') == 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="gestionnaire" {{ request('role') == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                                <option value="vendeur" {{ request('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-redo"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Liste des utilisateurs -->
                @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôles (RBAC)</th>
                                <th>Rôle Legacy</th>
                                <th>Permissions Directes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->nom }} {{ $user->prenom }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $userRoles = $user->roles;
                                    @endphp
                                    @if($userRoles->count() > 0)
                                        @foreach($userRoles as $role)
                                            <span class="badge bg-{{ $role->slug == 'super-admin' ? 'danger' : ($role->slug == 'admin' ? 'warning' : 'info') }} me-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Aucun rôle RBAC</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role)
                                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Compter les permissions via les rôles
                                        $totalPermissions = 0;
                                        foreach ($user->roles as $role) {
                                            $totalPermissions += $role->permissions->count();
                                        }
                                    @endphp
                                    @if($totalPermissions > 0)
                                        <span class="badge bg-success">{{ $totalPermissions }} permission(s) via rôles</span>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.role-permissions.show', $user) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Gérer
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun utilisateur trouvé.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

