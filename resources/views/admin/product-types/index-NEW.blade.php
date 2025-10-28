@extends('admin.layouts.app')

@section('title', 'Gestion des Types de Produits - Allo Mobile Admin')
@section('page-title', 'Gestion des Types de Produits')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Liste des Types de Produits</h4>
                    <small class="text-muted">Gérez les types de produits de votre catalogue</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.product-types.create') }}" class="btn btn-secondary">
                        <i class="fas fa-plus me-2"></i>Nouveau Type de Produit
                    </a>
                    <button class="btn btn-outline-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter me-2"></i>Masquer Filtres
                    </button>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if(isset($productTypes))
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $productTypes->total() }}</h5>
                            <small>Total Types</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $productTypes->where('is_active', true)->count() }}</h5>
                            <small>Actifs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $productTypes->where('is_active', false)->count() }}</h5>
                            <small>Inactifs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $productTypes->sum('products_count') }}</h5>
                            <small>Produits associés</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Filtres (TOUJOURS VISIBLES) -->
    <div class="row mb-4" id="filters-section">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.product-types.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Nom ou description..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="per_page" class="form-label">Par page</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.product-types.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Tableau principal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="20%">
                                        <i class="fas fa-tag me-1"></i>Nom
                                    </th>
                                    <th width="15%">
                                        <i class="fas fa-folder me-1"></i>Catégorie
                                    </th>
                                    <th width="20%">Description</th>
                                    <th width="15%">
                                        <i class="fas fa-list me-1"></i>Attributs
                                    </th>
                                    <th width="10%">Produits</th>
                                    <th width="10%">Statut</th>
                                    <th width="5%">Ordre</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productTypes as $productType)
                                <tr>
                                    <td>
                                        <strong>{{ $productType->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $productType->slug }}</small>
                                    </td>
                                    <td>
                                        @if($productType->category)
                                            <span class="badge bg-info">
                                                {{ $productType->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune catégorie</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($productType->description, 50) }}</td>
                                    <td>
                                        @if($productType->attributes->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($productType->attributes->take(3) as $attribute)
                                                    <span class="badge bg-light text-dark">
                                                        {{ $attribute->name }}
                                                    </span>
                                                @endforeach
                                                @if($productType->attributes->count() > 3)
                                                    <span class="badge bg-secondary">
                                                        +{{ $productType->attributes->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Aucun attribut</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $productType->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($productType->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Actif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $productType->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product-types.show', $productType) }}" class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product-types.destroy', $productType) }}" method="POST" id="delete-product-type-{{ $productType->id }}" class="d-inline delete-product-type-form">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger delete-product-type-btn" title="Supprimer"
                                                    data-form-id="delete-product-type-{{ $productType->id }}"
                                                    data-product-type-name="{{ $productType->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun type de produit trouvé</h5>
                                            <p class="text-muted">Commencez par créer votre premier type de produit pour organiser vos produits.</p>
                                            <a href="{{ route('admin.product-types.create') }}" class="btn btn-secondary">
                                                <i class="fas fa-plus me-2"></i>Créer le premier type de produit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($productTypes) && $productTypes->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted">
                                    Affichage de {{ $productTypes->firstItem() ?? 0 }} à {{ $productTypes->lastItem() ?? 0 }}
                                    sur {{ $productTypes->total() }} types de produits
                                </small>
                            </div>
                            <div>
                                {{ $productTypes->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Fonction pour masquer/afficher les filtres
function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    const button = event.target;

    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Masquer Filtres';
    } else {
        filtersSection.style.display = 'none';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Afficher Filtres';
    }
}

// Système de filtrage dynamique
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation du système de filtrage...');

    const form = document.getElementById('filterForm');

    if (!form) {
        console.error('❌ Formulaire filterForm introuvable');
        return;
    }

    console.log('✅ Formulaire trouvé');

    let filterTimeout = null;

    // 1. RECHERCHE AVEC AUTO-SUBMIT (debounce)
    const searchInput = document.getElementById('search');
    if (searchInput) {
        console.log('✅ Champ de recherche trouvé');
        searchInput.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            const delay = this.value.length > 2 ? 300 : 600;
            filterTimeout = setTimeout(() => {
                console.log('🔄 Recherche:', this.value);
                form.submit();
            }, delay);
        });
    }

    // 2. FILTRES SELECT AVEC AUTO-SUBMIT
    const selects = form.querySelectorAll('select');
    console.log('📋 Filtres select trouvés:', selects.length);

    selects.forEach(select => {
        select.addEventListener('change', function() {
            console.log('🔄 Filtre changé:', this.name, '=', this.value);
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                console.log('✅ Soumission du formulaire');
                form.submit();
            }, 200);
        });
    });

    // 3. BOUTONS DE SUPPRESSION
    const deleteButtons = document.querySelectorAll('.delete-product-type-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-form-id');
            const productTypeName = this.getAttribute('data-product-type-name');
            const deleteForm = document.getElementById(formId);

            if (!deleteForm) return;

            customConfirm(
                `Êtes-vous sûr de vouloir supprimer le type de produit <strong>"${productTypeName}"</strong> ? Cette action est irréversible.`,
                function() {
                    deleteForm.submit();
                },
                null,
                'Suppression de type de produit',
                'Oui, supprimer',
                'Annuler'
            );
        });
    });

    console.log('✅ Initialisation terminée');
});
</script>
@endsection

