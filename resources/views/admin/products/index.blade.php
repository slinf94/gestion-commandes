@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Allo Mobile Admin')
@section('page-title', 'Gestion des Produits')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-dismiss" data-dismiss-time="5000" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
                    <i class="fas fa-box me-2"></i>Liste des Produits
                </h3>
                <small class="opacity-75">Gérez votre catalogue de produits</small>
            </div>
            <div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-light" style="border-radius: 8px;">
                    <i class="fas fa-plus me-2"></i>+ Nouveau Produit
                </a>
            </div>
        </div>
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
<div class="card mb-4 border-0 shadow-sm" style="border-radius: 10px;">
    <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A;">
        <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
            <i class="fas fa-filter me-2"></i>Filtres et Recherche
        </h5>
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

            <!-- Boutons d'action -->
            <div class="row mt-3 align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <span class="badge bg-primary">{{ $stats['total'] }}</span>
                            <small class="text-muted ms-1">Total</small>
                        </div>
                        <div class="me-3">
                            <span class="badge bg-success">{{ $stats['active'] }}</span>
                            <small class="text-muted ms-1">Actifs</small>
                        </div>
                        <div class="me-3">
                            <span class="badge bg-warning">{{ $stats['inactive'] }}</span>
                            <small class="text-muted ms-1">Inactifs</small>
                        </div>
                        <div>
                            <span class="badge bg-secondary">{{ $stats['draft'] }}</span>
                            <small class="text-muted ms-1">Brouillons</small>
                        </div>
                    </div>
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
                            <th width="8%">Image</th>
                            <th width="8%">ID</th>
                            <th width="20%">Nom</th>
                            <th width="10%">Prix</th>
                            <th width="10%">Statut</th>
                            <th width="15%">Date de création</th>
                            <th width="25%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                @php
                                    // Utiliser les images préchargées
                                    $productImages = $product->images ?? collect();
                                    $mainImage = $productImages->where('type', 'principale')->first()
                                        ?? $productImages->sortBy('order')->first();
                                @endphp
                                @if($mainImage && isset($mainImage->url))
                                    <img src="{{ Storage::url($mainImage->url) }}"
                                         alt="{{ $product->name }}"
                                         class="img-thumbnail"
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;"
                                         loading="lazy"
                                         onerror="this.parentElement.innerHTML='<div class=\'bg-light d-flex align-items-center justify-content-center\' style=\'width: 60px; height: 60px; border-radius: 6px;\'><i class=\'fas fa-image text-muted\'></i></div>';">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px; border-radius: 6px; border: 1px solid #dee2e6;">
                                        <i class="fas fa-image text-muted" style="font-size: 24px;"></i>
                                    </div>
                                @endif
                            </td>
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
                                    <a href="{{ route('admin.products.variants.index', ['product' => $product->id]) }}"
                                       class="btn btn-sm btn-success" title="Gérer les variantes" style="border-radius: 6px;">
                                        <i class="fas fa-cubes me-1"></i>Variantes
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          id="delete-product-{{ $product->id }}"
                                          method="POST" class="d-inline delete-product-form">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-product-btn"
                                            data-form-id="delete-product-{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                            <option value="id" {{ request('sort_by', 'id') == 'id' ? 'selected' : '' }}>ID</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                            <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Prix</option>
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                            <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Statut</option>
                        </select>
                    </div>
                    <div class="me-3">
                        <select class="form-select form-select-sm" id="sortOrder" name="sort_order" onchange="updateSorting()">
                            <option value="asc" {{ request('sort_order', 'asc') == 'asc' ? 'selected' : '' }}>Croissant</option>
                            <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Décroissant</option>
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
// Système de recherche et filtrage dynamique - FONCTIONNEL
console.log('🚀 Initialisation du système de filtrage produits...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM chargé');

    let filterTimeout = null;

    // Récupérer le formulaire
    const form = document.getElementById('filterForm');

    if (!form) {
        console.error('❌ Formulaire filterForm introuvable');
        return;
    }

    console.log('✅ Formulaire trouvé');

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

    // 2. FILTRES AVEC AUTO-SUBMIT
    const filterInputs = form.querySelectorAll('select, input[type="number"], input[type="date"]');
    console.log('📋 Filtres trouvés:', filterInputs.length);

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            console.log('🔄 Filtre changé:', this.name, '=', this.value);
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                console.log('✅ Soumission du formulaire');
                form.submit();
            }, 300);
        });
    });

    // 3. BOUTONS DE SUPPRESSION AVEC MODAL
    function setupProductDeleteButtons() {
        const deleteButtons = document.querySelectorAll('.delete-product-btn');
        console.log('🗑️ Boutons de suppression:', deleteButtons.length);

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form-id');
                const productName = this.getAttribute('data-product-name');
                const deleteForm = document.getElementById(formId);

                if (!deleteForm) {
                    console.error('Formulaire de suppression introuvable');
                    return;
                }

                customConfirm(
                    `Êtes-vous sûr de vouloir supprimer le produit <strong>"${productName}"</strong> ? Cette action est irréversible.`,
                    function() {
                        deleteForm.submit();
                    },
                    null,
                    'Suppression de produit',
                    'Oui, supprimer',
                    'Annuler'
                );
            });
        });
    }

    setupProductDeleteButtons();
    console.log('✅ Initialisation terminée');
});

// Auto-dismiss des alertes de succès
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.auto-dismiss');
    alerts.forEach(function(alert) {
        const dismissTime = parseInt(alert.getAttribute('data-dismiss-time')) || 5000;
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, dismissTime);
    });
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
