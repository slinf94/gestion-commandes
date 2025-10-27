@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Allo Mobile Admin')
@section('page-title', 'Gestion des Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Liste des Produits</h4>
        <small class="text-muted">Gérez votre catalogue de produits</small>
    </div>
    <div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-secondary">
            <i class="fas fa-plus me-2"></i>Nouveau Produit
        </a>
    </div>
</div>

<!-- Message d'erreur si présent -->
@if(isset($error))
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ $error }}
</div>
@endif

<!-- Filtres avancés -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres et Recherche</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
            <div class="row g-3">
                <!-- Recherche -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nom, description, SKU...">
                </div>

                <!-- Statut -->
                <div class="col-md-2">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    </select>
                </div>

                <!-- Catégorie -->
                <div class="col-md-2">
                    <label for="category_id" class="form-label">Catégorie</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type de produit -->
                <div class="col-md-2">
                    <label for="product_type_id" class="form-label">Type</label>
                    <select class="form-select" id="product_type_id" name="product_type_id">
                        <option value="">Tous les types</option>
                        @foreach($productTypes as $type)
                            <option value="{{ $type->id }}" {{ request('product_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
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
            </div>

            <!-- Filtres avancés -->
            <div class="row g-3 mt-2" id="advancedFilters" style="display: none;">
                <div class="col-md-3">
                    <label for="price_min" class="form-label">Prix min (FCFA)</label>
                    <input type="number" class="form-control" id="price_min" name="price_min"
                           value="{{ request('price_min') }}" placeholder="0">
                </div>
                <div class="col-md-3">
                    <label for="price_max" class="form-label">Prix max (FCFA)</label>
                    <input type="number" class="form-control" id="price_max" name="price_max"
                           value="{{ request('price_max') }}" placeholder="1000000">
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="date_from" name="date_from"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="date_to" name="date_to"
                           value="{{ request('date_to') }}">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-times me-1"></i>Effacer
                    </a>
                    <button type="button" class="btn btn-outline-info" onclick="toggleAdvancedFilters()">
                        <i class="fas fa-cog me-1"></i>Filtres avancés
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        Total: {{ $stats['total'] }} |
                        Actifs: {{ $stats['active'] }} |
                        Inactifs: {{ $stats['inactive'] }} |
                        Brouillons: {{ $stats['draft'] }}
                    </small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des produits -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'draft' => 'warning'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'draft' => 'Brouillon'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$product->status] ?? ucfirst($product->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination améliorée -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Affichage de {{ $products->firstItem() ?? 0 }} à {{ $products->lastItem() ?? 0 }}
                        sur {{ $products->total() }} produits
                    </small>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Tri dynamique -->
                    <div class="me-3">
                        <select class="form-select form-select-sm" id="sortBy" name="sort_by" onchange="updateSorting()">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Prix</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Statut</option>
                        </select>
                    </div>
                    <div class="me-3">
                        <select class="form-select form-select-sm" id="sortOrder" name="sort_order" onchange="updateSorting()">
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Croissant</option>
                        </select>
                    </div>
                    <!-- Pagination -->
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun produit trouvé</h5>
                <p class="text-muted">Commencez par créer votre premier produit.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer un produit
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Filtres dynamiques
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire de filtre
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('select, input[type="text"], input[type="number"], input[type="date"]');

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Délai pour éviter trop de requêtes
            clearTimeout(window.filterTimeout);
            window.filterTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    });

    // Recherche en temps réel
    const searchInput = document.getElementById('search');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 1000);
    });
});

// Fonction pour basculer les filtres avancés
function toggleAdvancedFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    const button = event.target;

    if (advancedFilters.style.display === 'none') {
        advancedFilters.style.display = 'block';
        button.innerHTML = '<i class="fas fa-cog me-1"></i>Masquer filtres avancés';
    } else {
        advancedFilters.style.display = 'none';
        button.innerHTML = '<i class="fas fa-cog me-1"></i>Filtres avancés';
    }
}

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

// Fonction pour exporter les résultats
function exportResults() {
    const form = document.getElementById('filterForm');
    const url = new URL(form.action);

    // Ajouter tous les paramètres du formulaire
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        if (value) {
            url.searchParams.set(key, value);
        }
    }

    // Ajouter le paramètre d'export
    url.searchParams.set('export', 'csv');

    // Ouvrir le lien d'export
    window.open(url.toString(), '_blank');
}

// Fonction pour réinitialiser les filtres
function resetFilters() {
    const form = document.getElementById('filterForm');
    form.reset();
    form.submit();
}
</script>
@endsection
