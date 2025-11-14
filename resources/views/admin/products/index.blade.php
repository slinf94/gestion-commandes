@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Allo Mobile Admin')
@section('page-title', 'Gestion des Produits')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
    <div class="alert alert-danger alert-dismissible fade show auto-dismiss" data-dismiss-time="8000" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Erreur:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @php
        // Nettoyer le message d'erreur après affichage pour éviter la persistance
        session()->forget('error');
    @endphp
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
                <div class="col-md-3">
                    @include('admin.components.search-input', [
                        'id' => 'search',
                        'name' => 'search',
                        'placeholder' => 'Nom, description, SKU...',
                        'value' => request('search', ''),
                        'searchUrl' => route('admin.search.products'),
                        'resultKey' => 'data',
                        'minLength' => 2,
                        'debounceDelay' => 500
                    ])
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
                <div class="col-md-3">
                    <label for="per_page" class="form-label">Par page</label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <!-- Filtres avancés pour téléphones -->
            <div class="card shadow-sm border-0 mt-3" id="phoneFilters" style="display: none; border-radius: 10px;">
                <div class="card-header bg-primary text-white" style="border-radius: 10px 10px 0 0;">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-mobile-alt me-2"></i>Filtres Téléphones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="brand" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i>Marque
                            </label>
                            <select class="form-select form-select-sm" id="brand" name="brand" style="border-radius: 6px;">
                                <option value="">🔍 Toutes les marques</option>
                                @foreach($brands ?? [] as $brand)
                                    <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                        {{ $brand }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ count($brands ?? []) }} marques disponibles</small>
                        </div>
                        <div class="col-md-3">
                            <label for="range" class="form-label fw-semibold">
                                <i class="fas fa-layer-group me-1 text-primary"></i>Gamme
                            </label>
                            <select class="form-select form-select-sm" id="range" name="range" style="border-radius: 6px;">
                                <option value="">🔍 Toutes les gammes</option>
                                @foreach($ranges ?? [] as $range)
                                    <option value="{{ $range }}" {{ request('range') == $range ? 'selected' : '' }}>
                                        {{ $range }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ count($ranges ?? []) }} gammes disponibles</small>
                        </div>
                        <div class="col-md-3">
                            <label for="format" class="form-label fw-semibold">
                                <i class="fas fa-mobile-screen me-1 text-primary"></i>Format
                            </label>
                            <select class="form-select form-select-sm" id="format" name="format" style="border-radius: 6px;">
                                <option value="">🔍 Tous les formats</option>
                                @foreach($formats ?? [] as $format)
                                    <option value="{{ $format }}" {{ request('format') == $format ? 'selected' : '' }}>
                                        {{ $format }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ count($formats ?? []) }} formats disponibles</small>
                        </div>
                        <div class="col-md-3">
                            <label for="stock_available" class="form-label fw-semibold">
                                <i class="fas fa-box-check me-1 text-primary"></i>Disponibilité
                            </label>
                            <select class="form-select form-select-sm" id="stock_available" name="stock_available" style="border-radius: 6px;">
                                <option value="">🔍 Tous</option>
                                <option value="yes" {{ request('stock_available') == 'yes' ? 'selected' : '' }}>✅ En stock</option>
                                <option value="no" {{ request('stock_available') == 'no' ? 'selected' : '' }}>❌ Rupture</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres avancés pour accessoires -->
            <div class="card shadow-sm border-0 mt-3" id="accessoryFilters" style="display: none; border-radius: 10px;">
                <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-plug me-2"></i>Filtres Accessoires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="type_accessory" class="form-label fw-semibold">
                                <i class="fas fa-puzzle-piece me-1 text-success"></i>Type d'accessoire
                            </label>
                            <select class="form-select form-select-sm" id="type_accessory" name="type_accessory" style="border-radius: 6px;">
                                <option value="">🔍 Tous les types</option>
                                @foreach($accessoryTypes ?? [] as $type)
                                    <option value="{{ $type }}" {{ request('type_accessory') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ count($accessoryTypes ?? []) }} types disponibles</small>
                        </div>
                        <div class="col-md-4">
                            <label for="compatibility" class="form-label fw-semibold">
                                <i class="fas fa-link me-1 text-success"></i>Compatibilité
                            </label>
                            <select class="form-select form-select-sm" id="compatibility" name="compatibility" style="border-radius: 6px;">
                                <option value="">🔍 Toutes compatibilités</option>
                                @foreach($compatibilities ?? [] as $compatibility)
                                    <option value="{{ $compatibility }}" {{ request('compatibility') == $compatibility ? 'selected' : '' }}>
                                        {{ $compatibility }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ count($compatibilities ?? []) }} compatibilités disponibles</small>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_available_accessory" class="form-label fw-semibold">
                                <i class="fas fa-box-check me-1 text-success"></i>Disponibilité
                            </label>
                            <select class="form-select form-select-sm" id="stock_available_accessory" name="stock_available" style="border-radius: 6px;">
                                <option value="">🔍 Tous</option>
                                <option value="yes" {{ request('stock_available') == 'yes' ? 'selected' : '' }}>✅ En stock</option>
                                <option value="no" {{ request('stock_available') == 'no' ? 'selected' : '' }}>❌ Rupture</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres prix -->
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="price_min" class="form-label">Prix min (FCFA)</label>
                    <input type="number" class="form-control" id="price_min" name="price_min" 
                           value="{{ request('price_min') }}" placeholder="0">
                </div>
                <div class="col-md-3">
                    <label for="price_max" class="form-label">Prix max (FCFA)</label>
                    <input type="number" class="form-control" id="price_max" name="price_max" 
                           value="{{ request('price_max') }}" placeholder="999999">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-primary btn-sm me-2" id="toggleAdvancedFilters" style="border-radius: 8px;">
                        <i class="fas fa-filter me-1"></i>Filtres Avancés
                        <span class="badge bg-light text-primary ms-2" id="filterCount" style="display: none;">0</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllFilters()">
                        <i class="fas fa-eraser me-1"></i>Tout Effacer
                    </button>
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
                        <div class="me-3">
                            <span class="badge bg-secondary">{{ $stats['draft'] }}</span>
                            <small class="text-muted ms-1">Brouillons</small>
                        </div>
                        @if(isset($stats['in_stock']))
                        <div class="me-3">
                            <span class="badge bg-info">{{ $stats['in_stock'] }}</span>
                            <small class="text-muted ms-1">En stock</small>
                        </div>
                        @endif
                        @if(isset($stats['out_of_stock']))
                        <div>
                            <span class="badge bg-danger">{{ $stats['out_of_stock'] }}</span>
                            <small class="text-muted ms-1">Rupture</small>
                        </div>
                        @endif
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
                                    // Utiliser main_image si disponible (optimisé), sinon essayer images
                                    $mainImage = null;
                                    if (isset($product->main_image) && !empty($product->main_image)) {
                                        $mainImage = (object)['url' => $product->main_image];
                                    } elseif (isset($product->images) && is_iterable($product->images)) {
                                        $productImages = is_array($product->images) ? collect($product->images) : $product->images;
                                        $mainImage = $productImages->where('type', 'principale')->first()
                                            ?? $productImages->sortBy('order')->first();
                                    }
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
                                    $isActive = $product->status === 'active';
                                @endphp
                                @if($product->status === 'draft')
                                    <span class="badge bg-warning">Brouillon</span>
                                @else
                                @php
                                    // Utiliser le slug déjà préparé dans le contrôleur
                                    $toggleSlug = $product->slug ?? ('no-slug-' . $product->id);
                                @endphp
                                    <button type="button"
                                            class="btn btn-sm product-toggle-btn {{ $isActive ? 'btn-outline-secondary' : 'btn-success' }}"
                                            data-product-slug="{{ $toggleSlug }}"
                                            data-current-status="{{ $product->status }}">
                                        @if($isActive)
                                            <i class="fas fa-user-slash me-1"></i>Désactiver
                                        @else
                                            <i class="fas fa-check me-1"></i>Activer
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @php
                                        // Utiliser le slug déjà préparé dans le contrôleur
                                        $showSlug = $product->slug ?? ('no-slug-' . $product->id);
                                    @endphp
                                    <a href="{{ route('admin.products.show', $showSlug) }}"
                                       class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @php $canEdit = auth()->user() && (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin') || in_array(auth()->user()->role,['super-admin','admin'])); @endphp
                                    @if($canEdit)
                                        @php
                                            // Utiliser le slug déjà préparé dans le contrôleur (pas de requête DB ici)
                                            $editSlug = !empty($product->slug) ? $product->slug : ('no-slug-' . $product->id);
                                        @endphp
                                        <a href="{{ route('admin.products.edit', $editSlug) }}"
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.products.variants.index', ['product' => $product->id]) }}"
                                           class="btn btn-sm btn-success" title="Gérer les variantes" style="border-radius: 6px;">
                                            <i class="fas fa-cubes me-1"></i>Variantes
                                        </a>
                                        @php
                                            // S'assurer d'avoir un slug valide pour destroy
                                            $destroySlug = !empty($product->slug) ? $product->slug : (\Illuminate\Support\Str::slug($product->name) . '-' . $product->id);
                                        @endphp
                                        <form action="{{ route('admin.products.destroy', $destroySlug) }}"
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
                                    @endif
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
                        @php
                            $firstItem = method_exists($products, 'firstItem') ? $products->firstItem() : (($products->currentPage() - 1) * $products->perPage() + 1);
                            $lastItem = method_exists($products, 'lastItem') ? $products->lastItem() : ($products->currentPage() * $products->perPage());
                            $total = method_exists($products, 'total') ? $products->total() : '?';
                        @endphp
                        Affichage de {{ $firstItem }} à {{ $lastItem }}
                        @if($total !== '?')
                            sur {{ $total }} produits
                        @else
                            (page {{ $products->currentPage() }})
                        @endif
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

    // 1. RECHERCHE AVEC AUTO-SUBMIT (debounce) - SEULEMENT SI PAS D'AUTOCOMPLETE
    const searchInput = document.getElementById('search');
    if (searchInput && !searchInput.hasAttribute('data-autocomplete-initialized')) {
        console.log('✅ Champ de recherche trouvé (mode formulaire)');
        searchInput.addEventListener('input', function(e) {
            // Ne pas soumettre si l'autocomplete est actif
            if (this.closest('.search-autocomplete-wrapper')) {
                return;
            }
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

// Toggle filtres avancés
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleAdvancedFilters');
    const phoneFilters = document.getElementById('phoneFilters');
    const accessoryFilters = document.getElementById('accessoryFilters');
    const filterCount = document.getElementById('filterCount');
    
    // Fonction pour compter les filtres actifs
    function countActiveFilters() {
        let count = 0;
        if (document.getElementById('brand')?.value) count++;
        if (document.getElementById('range')?.value) count++;
        if (document.getElementById('format')?.value) count++;
        if (document.getElementById('type_accessory')?.value) count++;
        if (document.getElementById('compatibility')?.value) count++;
        if (document.getElementById('stock_available')?.value) count++;
        if (document.getElementById('stock_available_accessory')?.value) count++;
        if (document.getElementById('price_min')?.value) count++;
        if (document.getElementById('price_max')?.value) count++;
        return count;
    }
    
    // Mettre à jour le compteur
    function updateFilterCount() {
        const count = countActiveFilters();
        if (filterCount) {
            filterCount.textContent = count;
            filterCount.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isVisible = phoneFilters.style.display !== 'none' || accessoryFilters.style.display !== 'none';
            phoneFilters.style.display = isVisible ? 'none' : 'block';
            accessoryFilters.style.display = isVisible ? 'none' : 'block';
            const count = countActiveFilters();
            const icon = isVisible ? '<i class="fas fa-filter me-1"></i>' : '<i class="fas fa-times me-1"></i>';
            const text = isVisible ? 'Filtres Avancés' : 'Masquer Filtres';
            const badge = count > 0 ? ' <span class="badge bg-light text-primary ms-2" id="filterCount">' + count + '</span>' : '';
            this.innerHTML = icon + text + badge;
        });
    }

    // Afficher les filtres si des valeurs sont présentes
    const hasPhoneFilters = document.getElementById('brand')?.value || document.getElementById('range')?.value || document.getElementById('format')?.value;
    const hasAccessoryFilters = document.getElementById('type_accessory')?.value || document.getElementById('compatibility')?.value;
    
    if (phoneFilters && hasPhoneFilters) {
        phoneFilters.style.display = 'block';
    }
    if (accessoryFilters && hasAccessoryFilters) {
        accessoryFilters.style.display = 'block';
    }
    
    if ((hasPhoneFilters || hasAccessoryFilters) && toggleBtn) {
        const count = countActiveFilters();
        const badge = count > 0 ? ' <span class="badge bg-light text-primary ms-2" id="filterCount">' + count + '</span>' : '';
        toggleBtn.innerHTML = '<i class="fas fa-times me-1"></i>Masquer Filtres' + badge;
    }
    
    // Mettre à jour le compteur au chargement
    updateFilterCount();
    
    // Mettre à jour le compteur quand les filtres changent
    ['brand', 'range', 'format', 'type_accessory', 'compatibility', 'stock_available', 'stock_available_accessory', 'price_min', 'price_max'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', updateFilterCount);
        }
    });
});

// Fonction pour effacer tous les filtres
function clearAllFilters() {
    const form = document.getElementById('filterForm');
    if (form) {
        form.reset();
        form.submit();
    }
}

// Toggle statut produit (AJAX)
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.querySelectorAll('.product-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productSlug = this.getAttribute('data-product-slug');
            const current = this.getAttribute('data-current-status');
            const actionText = current === 'active' ? 'désactiver' : 'activer';
            const that = this;

            const doRequest = () => {
                that.disabled = true;
                fetch(`/admin/products/${productSlug}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                .then(async res => {
                    const isJson = (res.headers.get('content-type') || '').includes('application/json');
                    const data = isJson ? await res.json() : {};
                    if (!res.ok || (data && data.success === false)) {
                        throw new Error((data && data.message) || `Erreur ${res.status}`);
                    }
                    return data;
                })
                .then(() => {
                    showAlert(`Produit ${actionText} avec succès`, 'success');
                    setTimeout(() => window.location.reload(), 600);
                })
                .catch(err => {
                    showAlert(err.message || 'Erreur lors de la mise à jour', 'error');
                })
                .finally(() => { that.disabled = false; });
            };

            if (current === 'active') {
                customConfirm(`Voulez-vous vraiment désactiver ce produit ?`, () => doRequest(), null, 'Confirmation');
            } else {
                doRequest();
            }
        });
    });
});
</script>
@endsection
