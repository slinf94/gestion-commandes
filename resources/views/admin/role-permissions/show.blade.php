@extends('admin.layouts.app')

@section('title', 'Gestion des Profils et Droits - ' . $user->nom)
@section('page-title', 'Gestion des Profils et Droits')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user-shield me-2"></i>
                    Profils et Droits d'Accès - {{ $user->nom }} {{ $user->prenom }}
                </h5>
                <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body">
                <!-- Informations utilisateur -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary">Informations utilisateur</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>{{ $user->nom }} {{ $user->prenom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Téléphone:</strong></td>
                                <td>{{ $user->numero_telephone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Rôles RBAC -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    Rôles RBAC ({{ $user->roles->count() }})
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($user->roles->count() > 0)
                                    <ul class="list-group mb-3">
                                        @foreach($user->roles as $role)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $role->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $role->slug }}</small>
                                                @if($role->permissions->count() > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $role->permissions->count() }} permission(s) associée(s)
                                                    </small>
                                                @endif
                                            </div>
                                            @if($role->slug !== 'super-admin' || \App\Models\User::where('role', 'super-admin')->count() > 1)
                                                <form method="POST" action="{{ route('admin.role-permissions.remove-role', $user) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="role_id" value="{{ $role->id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir retirer ce rôle ?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Aucun rôle RBAC assigné</p>
                                @endif

                                <!-- Assigner un nouveau rôle -->
                                <div class="mt-3">
                                    <h6>Assigner un nouveau rôle</h6>
                                    <form method="POST" action="{{ route('admin.role-permissions.assign-role', $user) }}">
                                        @csrf
                                        <div class="input-group">
                                            <select name="role_id" class="form-select" required>
                                                <option value="">Sélectionner un rôle...</option>
                                                @foreach($allRoles as $role)
                                                    @if(!$user->hasRole($role->slug))
                                                        <option value="{{ $role->id }}">{{ $role->name }} ({{ $role->slug }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Assigner
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rôle Legacy -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-cog me-2"></i>
                                    Rôle Legacy
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.role-permissions.update-legacy-role', $user) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Rôle (champ legacy)</label>
                                        <select name="role" id="role" class="form-select" required>
                                            <option value="client" {{ $user->role == 'client' ? 'selected' : '' }}>Client</option>
                                            <option value="vendeur" {{ $user->role == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                                            <option value="gestionnaire" {{ $user->role == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="super-admin" {{ $user->role == 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                </form>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle"></i> Ce champ est utilisé pour la compatibilité avec l'ancien système.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions via les rôles -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-key me-2"></i>
                                    Permissions (via les rôles)
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $allUserPermissions = collect();
                                    foreach ($user->roles as $role) {
                                        $allUserPermissions = $allUserPermissions->merge($role->permissions);
                                    }
                                    $allUserPermissions = $allUserPermissions->unique('id');
                                @endphp
                                
                                @if($allUserPermissions->count() > 0)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Les permissions sont gérées via les rôles assignés. Les permissions suivantes sont héritées de ses rôles :
                                    </div>
                                    <div class="row">
                                        @foreach($allUserPermissions as $permission)
                                        <div class="col-md-4 mb-2">
                                            <div class="card border-success">
                                                <div class="card-body p-2">
                                                    <div>
                                                        <strong>{{ $permission->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $permission->slug }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">Aucune permission assignée via les rôles</p>
                                @endif

                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Information :</strong> Dans ce système, les permissions sont gérées uniquement via les rôles. Pour modifier les permissions d'un utilisateur, assignez ou retirez des rôles. Cette approche garantit une meilleure organisation et une maintenance simplifiée des droits d'accès.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

