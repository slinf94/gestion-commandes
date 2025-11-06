@extends('admin.layouts.app')

@section('title', 'Gestion des Profils et Droits d\'Accès - Allo Mobile')
@section('page-title', 'Gestion des Profils et Droits d\'Accès')

@section('content')
<!-- Header moderne avec gradient vert Allo Mobile -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
            <i class="fas fa-user-shield me-2"></i>Gestion des Profils et Droits d'Accès
        </h3>
        <small class="opacity-75">Allo Mobile - Administration des rôles et permissions</small>
    </div>
</div>

<div class="container-fluid">
    <!-- Message informatif -->
    <div class="alert alert-light border-start border-3 border-info mb-4" style="border-radius: 12px; background: #f8f9fa;">
        <i class="fas fa-info-circle text-info me-2"></i>
        <strong>Information :</strong> Gérez les rôles et permissions des utilisateurs administrateurs. Seul le Super Administrateur peut accéder à cette interface.
    </div>

    <!-- Section de recherche et filtres -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
            <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                <i class="fas fa-filter me-2"></i>Recherche et Filtres
            </h5>
        </div>
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.role-permissions.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-5">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Rechercher par nom, prénom ou email..." 
                                   value="{{ request('search') }}"
                                   style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="role" class="form-label">Rôle</label>
                        <select name="role" id="role" class="form-select" style="border-radius: 8px;">
                            <option value="">Tous les rôles</option>
                            <option value="super-admin" {{ request('role') == 'super-admin' ? 'selected' : '' }}>Super Administrateur</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                            <option value="gestionnaire" {{ request('role') == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                            <option value="vendeur" {{ request('role') == 'vendeur' ? 'selected' : '' }}>Vendeur</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-success flex-fill" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </button>
                        <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    @if($users->count() > 0)
    <div class="card border-0 shadow-lg" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <tr>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A;">
                                <i class="fas fa-user me-2 text-primary"></i>Utilisateur
                            </th>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A;">
                                <i class="fas fa-envelope me-2 text-primary"></i>Email
                            </th>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A;">
                                <i class="fas fa-user-tag me-2 text-primary"></i>Rôles (RBAC)
                            </th>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A;">
                                <i class="fas fa-history me-2 text-primary"></i>Rôle Legacy
                            </th>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A;">
                                <i class="fas fa-key me-2 text-primary"></i>Permissions Directes
                            </th>
                            <th style="padding: 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #38B04A; text-align: center;">
                                <i class="fas fa-cog me-2 text-primary"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 15px; vertical-align: middle;">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; background: linear-gradient(135deg, #38B04A, #4CAF50); color: white; font-weight: 600;">
                                            {{ strtoupper(substr($user->nom, 0, 1) . substr($user->prenom, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong style="color: #212529; font-size: 1rem;">{{ $user->nom }} {{ $user->prenom }}</strong>
                                        <br>
                                        <small class="text-muted" style="font-size: 0.85rem;">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                <span style="color: #495057;">{{ $user->email }}</span>
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                @php
                                    $userRoles = $user->roles;
                                @endphp
                                @if($userRoles->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($userRoles as $role)
                                            @php
                                                $roleColors = [
                                                    'super-admin' => ['bg' => 'background: linear-gradient(135deg, #dc3545, #c82333);', 'text' => 'white'],
                                                    'admin' => ['bg' => 'background: linear-gradient(135deg, #ffc107, #ffb300);', 'text' => 'white'],
                                                    'gestionnaire' => ['bg' => 'background: linear-gradient(135deg, #0d6efd, #0b5ed7);', 'text' => 'white'],
                                                    'vendeur' => ['bg' => 'background: linear-gradient(135deg, #0dcaf0, #0aa2c0);', 'text' => 'white'],
                                                ];
                                                $color = $roleColors[$role->slug] ?? ['bg' => 'background: #6c757d;', 'text' => 'white'];
                                            @endphp
                                            <span class="badge" style="{{ $color['bg'] }} color: {{ $color['text'] }}; padding: 6px 12px; border-radius: 8px; font-weight: 500; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus-circle me-1"></i>Aucun rôle RBAC
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                @if($user->role)
                                    <span class="badge" style="background: #6c757d; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-minus-circle me-1"></i>Non défini
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                @php
                                    // Compter les permissions via les rôles
                                    $totalPermissions = 0;
                                    foreach ($user->roles as $role) {
                                        $totalPermissions += $role->permissions->count();
                                    }
                                @endphp
                                @if($totalPermissions > 0)
                                    <span class="badge" style="background: linear-gradient(135deg, #38B04A, #4CAF50); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 500; box-shadow: 0 2px 4px rgba(56, 176, 74, 0.3);">
                                        <i class="fas fa-check-circle me-1"></i>{{ $totalPermissions }} permission(s) via rôles
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-times-circle me-1"></i>Aucune
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                <a href="{{ route('admin.role-permissions.show', $user) }}" 
                                   class="btn btn-sm" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; color: white; border-radius: 8px; padding: 8px 16px; font-weight: 500; box-shadow: 0 2px 4px rgba(56, 176, 74, 0.3); transition: all 0.3s;">
                                    <i class="fas fa-edit me-1"></i>Gérer
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="card-footer bg-white" style="border-top: 1px solid #e9ecef; border-radius: 0 0 12px 12px;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.9rem;">
                    Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats
                </div>
                <div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
            <h5 class="text-muted mb-2">Aucun utilisateur trouvé</h5>
            <p class="text-muted">Aucun utilisateur ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.role-permissions.index') }}" class="btn btn-success mt-3" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; border-radius: 8px;">
                <i class="fas fa-redo me-2"></i>Réinitialiser les filtres
            </a>
        </div>
    </div>
    @endif
</div>

<style>
/* Styles pour mobile */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: 12px;
        overflow-x: auto;
    }
    
    .table thead {
        display: none;
    }
    
    .table tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table tbody td {
        display: block;
        padding: 10px 0;
        text-align: left;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table tbody td:last-child {
        border-bottom: none;
    }
    
    .table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #38B04A;
        display: block;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }
    
    .table tbody td:first-child::before {
        content: "Utilisateur";
    }
    
    .card-header h3 {
        font-size: 1.25rem !important;
    }
    
    .card-body {
        padding: 15px !important;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
}

/* Animations et effets hover */
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(56, 176, 74, 0.4) !important;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s;
}

.badge {
    transition: transform 0.2s;
}

.badge:hover {
    transform: scale(1.05);
}

/* Styles pour les badges de rôles */
.gap-1 > * + * {
    margin-left: 0.25rem;
}

.gap-2 > * + * {
    margin-left: 0.5rem;
}

/* Amélioration des formulaires */
.form-control:focus,
.form-select:focus {
    border-color: #38B04A;
    box-shadow: 0 0 0 0.2rem rgba(56, 176, 74, 0.25);
}

/* Styles pour la pagination */
.pagination .page-link {
    color: #38B04A;
    border-color: #dee2e6;
}

.pagination .page-link:hover {
    color: #4CAF50;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #38B04A, #4CAF50);
    border-color: #38B04A;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit sur changement de sélection
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }
    
    // Ajout de labels pour mobile
    if (window.innerWidth <= 768) {
        const cells = document.querySelectorAll('.table tbody td');
        const headers = ['Utilisateur', 'Email', 'Rôles (RBAC)', 'Rôle Legacy', 'Permissions Directes', 'Actions'];
        
        cells.forEach((cell, index) => {
            const rowIndex = Math.floor(index / 6);
            const colIndex = index % 6;
            if (colIndex < headers.length) {
                cell.setAttribute('data-label', headers[colIndex]);
            }
        });
    }
    
    // Animation au chargement
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection

