@extends('admin.layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
                                <i class="fas fa-tags me-2"></i>Gestion des Catégories
                            </h3>
                            <small class="opacity-75">Organisez et gérez vos catégories de produits</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-light" style="border-radius: 8px;">
                                <i class="fas fa-plus me-2"></i> Nouvelle Catégorie
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-time="5000">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filtres avancés -->
                    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A;">
                            <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                                <i class="fas fa-filter me-2"></i>Filtres et Recherche
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.categories.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <!-- Recherche -->
                                    <div class="col-md-4">
                                        <label for="search" class="form-label">Recherche</label>
                                        <input type="text" class="form-control" id="search" name="search"
                                               value="{{ request('search') }}" placeholder="Nom, description...">
                                    </div>

                                    <!-- Statut -->
                                    <div class="col-md-2">
                                        <label for="is_active" class="form-label">Statut</label>
                                        <select class="form-select" id="is_active" name="is_active">
                                            <option value="">Tous les statuts</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Actif</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </div>

                                    <!-- Vedette -->
                                    <div class="col-md-2">
                                        <label for="is_featured" class="form-label">Vedette</label>
                                        <select class="form-select" id="is_featured" name="is_featured">
                                            <option value="">Toutes</option>
                                            <option value="1" {{ request('is_featured') == '1' ? 'selected' : '' }}>Vedette</option>
                                            <option value="0" {{ request('is_featured') == '0' ? 'selected' : '' }}>Normale</option>
                                        </select>
                                    </div>

                                    <!-- Pagination -->
                                    <div class="col-md-2">
                                        <label for="per_page" class="form-label">Par page</label>
                                        <select class="form-select" id="per_page" name="per_page">
                                            <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
                                            <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                        </select>
                                    </div>

                                    <!-- Actions -->
                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-success" style="border-radius: 8px; background: linear-gradient(135deg, #38B04A, #4CAF50); border: none;">
                                            <i class="fas fa-filter me-1"></i>Filtrer
                                        </button>
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                            <i class="fas fa-times me-1"></i>Effacer
                                        </a>
                                    </div>
                                </div>

                                <!-- Statistiques -->
                                <div class="row mt-4">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(135deg, #E3F2FD, #BBDEFB); border-left: 4px solid #2196F3;">
                                            <i class="fas fa-tags fa-2x me-3" style="color: #2196F3;"></i>
                                            <div>
                                                <div class="text-muted small">Total</div>
                                                <div class="h5 mb-0 fw-bold" style="color: #2196F3;">{{ $stats['total'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border-left: 4px solid #4CAF50;">
                                            <i class="fas fa-check-circle fa-2x me-3" style="color: #4CAF50;"></i>
                                            <div>
                                                <div class="text-muted small">Actives</div>
                                                <div class="h5 mb-0 fw-bold" style="color: #4CAF50;">{{ $stats['active'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(135deg, #FFF3E0, #FFE0B2); border-left: 4px solid #FF9800;">
                                            <i class="fas fa-pause-circle fa-2x me-3" style="color: #FF9800;"></i>
                                            <div>
                                                <div class="text-muted small">Inactives</div>
                                                <div class="h5 mb-0 fw-bold" style="color: #FF9800;">{{ $stats['inactive'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(135deg, #F3E5F5, #E1BEE7); border-left: 4px solid #9C27B0;">
                                            <i class="fas fa-star fa-2x me-3" style="color: #9C27B0;"></i>
                                            <div>
                                                <div class="text-muted small">Vedettes</div>
                                                <div class="h5 mb-0 fw-bold" style="color: #9C27B0;">{{ $stats['featured'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Parent</th>
                                    <th>Produits</th>
                                    <th>Statut</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }}"></i>
                                            @endif
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->is_featured)
                                                <span class="badge bg-warning ms-2">Vedette</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-info">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Racine</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $category->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                                  id="delete-category-{{ $category->id }}"
                                                  class="d-inline delete-category-form">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger delete-category-btn"
                                                    data-form-id="delete-category-{{ $category->id }}"
                                                    data-category-name="{{ $category->name }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Affichage des sous-catégories --}}
                                @if($category->children_count > 0)
                                    {{-- Note: Les sous-catégories ne sont pas chargées pour des raisons de performance --}}
                                    <tr class="table-light">
                                        <td colspan="6" class="text-center text-muted">
                                            <small>{{ $category->children_count }} sous-catégorie(s) disponible(s)</small>
                                        </td>
                                    </tr>
                                @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>Aucune catégorie trouvée</p>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Créer la première catégorie
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination améliorée -->
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <small class="text-muted">
                                Affichage de {{ $categories->firstItem() ?? 0 }} à {{ $categories->lastItem() ?? 0 }}
                                sur {{ $categories->total() }} catégories
                            </small>
                        </div>
                        <div class="d-flex align-items-center">
                            <!-- Tri dynamique -->
                            <div class="me-3">
                                <select class="form-select form-select-sm" id="sortBy" name="sort_by" onchange="updateSorting()">
                                    <option value="sort_order" {{ request('sort_by') == 'sort_order' ? 'selected' : '' }}>Ordre de tri</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                </select>
                            </div>
                            <div class="me-3">
                                <select class="form-select form-select-sm" id="sortOrder" name="sort_order" onchange="updateSorting()">
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Croissant</option>
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                                </select>
                            </div>
                            <!-- Pagination -->
                            <div>
                                {{ $categories->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Filtres dynamiques - FONCTIONNEL
console.log('🚀 Initialisation du système de filtrage...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM chargé');

    // Récupérer le formulaire
    const filterForm = document.getElementById('filterForm');

    if (!filterForm) {
        console.error('❌ Formulaire filterForm introuvable');
        return;
    }

    console.log('✅ Formulaire trouvé:', filterForm);

    let filterTimeout;
    let searchTimeout;

    // 1. GÉRER LES FILTRES (select, number, date)
    const filterInputs = filterForm.querySelectorAll('select, input[type="number"], input[type="date"]');
    console.log('📋 Filtres trouvés:', filterInputs.length);

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            console.log('🔄 Filtre changé:', this.name, '=', this.value);
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                console.log('✅ Soumission du formulaire de filtre');
                filterForm.submit();
            }, 300);
        });
    });

    // 2. GÉRER LA RECHERCHE (input text)
    const searchInput = document.getElementById('search');

    if (searchInput) {
        console.log('✅ Champ de recherche trouvé');

        searchInput.addEventListener('input', function() {
            console.log('🔄 Recherche tapée:', this.value);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                console.log('✅ Soumission du formulaire de recherche');
                filterForm.submit();
            }, 500);
        });
    } else {
        console.warn('⚠️ Champ de recherche introuvable');
    }

    // 3. GÉRER LES BOUTONS DE SUPPRESSION
    function setupDeleteButtons() {
        const deleteButtons = document.querySelectorAll('.delete-category-btn');
        console.log('🗑️ Boutons de suppression trouvés:', deleteButtons.length);

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form-id');
                const categoryName = this.getAttribute('data-category-name');
                const form = document.getElementById(formId);

                if (!form) {
                    console.error('Formulaire de suppression introuvable');
                    return;
                }

                customConfirm(
                    `Êtes-vous sûr de vouloir supprimer la catégorie <strong>"${categoryName}"</strong> ? Cette action est irréversible.`,
                    function() {
                        form.submit();
                    },
                    null,
                    'Suppression de catégorie',
                    'Oui, supprimer',
                    'Annuler'
                );
            });
        });
    }

    setupDeleteButtons();
    console.log('✅ Initialisation terminée');
});

// Fonction pour mettre à jour le tri
function updateSorting() {
    const sortBy = document.getElementById('sortBy').value;
    const sortOrder = document.getElementById('sortOrder').value;

    // Ajouter les paramètres de tri à l'URL
    const url = new URL(window.location);
    url.searchParams.set('sort_by', sortBy);
    url.searchParams.set('sort_order', sortOrder);

    // Rediriger avec les nouveaux paramètres
    window.location.href = url.toString();
}

// Auto-dismiss des alertes de succès
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.auto-dismiss');
    alerts.forEach(function(alert) {
        const dismissTime = parseInt(alert.getAttribute('data-dismiss-time')) || 5000; // 5 secondes par défaut
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, dismissTime);
    });
});
</script>
@endsection
