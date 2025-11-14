@extends('admin.layouts.app')

@section('title', 'Créer un Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Messages d'erreur/succès -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-exclamation-triangle"></i> Erreurs de validation</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); padding: 20px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
                                <i class="fas fa-plus-circle me-2"></i>Créer un Nouveau Produit
                            </h3>
                            <small class="opacity-75">Remplissez les informations pour créer un nouveau produit</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light btn-sm" style="border-radius: 8px;">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Informations Générales -->
                                <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nom du produit *</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                           id="name" name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sku" class="form-label">SKU</label>
                                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                           id="sku" name="sku" value="{{ old('sku') }}">
                                                    @error('sku')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Généré automatiquement si vide</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label">Catégorie *</label>
                                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                                            id="category_id" name="category_id" required>
                                                        <option value="">Sélectionner une catégorie</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="product_type_id" class="form-label">Type de Produit</label>
                                                    <select class="form-select @error('product_type_id') is-invalid @enderror"
                                                            id="product_type_id" name="product_type_id">
                                                        <option value="">Sélectionner un type</option>
                                                        @foreach($productTypes as $productType)
                                                            <option value="{{ $productType->id }}" {{ old('product_type_id') == $productType->id ? 'selected' : '' }}>
                                                                {{ $productType->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_type_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Champs e-commerce pour téléphones et accessoires -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="brand" class="form-label">Marque</label>
                                                    <select class="form-select select2-tags @error('brand') is-invalid @enderror"
                                                            id="brand" name="brand" data-placeholder="Rechercher ou saisir une marque...">
                                                        <option value="">{{ old('brand') ? '' : 'Rechercher ou saisir...' }}</option>
                                                        @if(old('brand') && !in_array(old('brand'), $brands->toArray()))
                                                            <option value="{{ old('brand') }}" selected>{{ old('brand') }}</option>
                                                        @endif
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand }}" {{ old('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('brand')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Pour téléphones - Recherchez ou saisissez une nouvelle marque</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="range" class="form-label">Gamme</label>
                                                    <select class="form-select select2-tags @error('range') is-invalid @enderror"
                                                            id="range" name="range" data-placeholder="Rechercher ou saisir une gamme...">
                                                        <option value="">{{ old('range') ? '' : 'Rechercher ou saisir...' }}</option>
                                                        @if(old('range') && !in_array(old('range'), $ranges->toArray()))
                                                            <option value="{{ old('range') }}" selected>{{ old('range') }}</option>
                                                        @endif
                                                        @foreach($ranges as $range)
                                                            <option value="{{ $range }}" {{ old('range') == $range ? 'selected' : '' }}>{{ $range }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('range')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Pour téléphones - Recherchez ou saisissez une nouvelle gamme</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="format" class="form-label">Format</label>
                                                    <select class="form-select select2-tags @error('format') is-invalid @enderror"
                                                            id="format" name="format" data-placeholder="Sélectionner ou saisir un format...">
                                                        <option value="">{{ old('format') ? '' : 'Sélectionner ou saisir...' }}</option>
                                                        @php
                                                            $defaultFormats = ['tactile', 'à touches', 'tablette Android'];
                                                            $allFormats = $formats->merge($defaultFormats)->unique()->sort();
                                                        @endphp
                                                        @if(old('format') && !in_array(old('format'), $allFormats->toArray()))
                                                            <option value="{{ old('format') }}" selected>{{ old('format') }}</option>
                                                        @endif
                                                        @foreach($allFormats as $format)
                                                            <option value="{{ $format }}" {{ old('format') == $format ? 'selected' : '' }}>{{ $format }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('format')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Pour téléphones - Recherchez ou saisissez un nouveau format</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="type_accessory" class="form-label">Type d'accessoire</label>
                                                    <select class="form-select select2-tags @error('type_accessory') is-invalid @enderror"
                                                            id="type_accessory" name="type_accessory" data-placeholder="Rechercher ou saisir un type...">
                                                        <option value="">{{ old('type_accessory') ? '' : 'Rechercher ou saisir...' }}</option>
                                                        @if(old('type_accessory') && !in_array(old('type_accessory'), $accessoryTypes->toArray()))
                                                            <option value="{{ old('type_accessory') }}" selected>{{ old('type_accessory') }}</option>
                                                        @endif
                                                        @foreach($accessoryTypes as $type)
                                                            <option value="{{ $type }}" {{ old('type_accessory') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('type_accessory')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Pour accessoires - Recherchez ou saisissez un nouveau type</small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="compatibility" class="form-label">Compatibilité</label>
                                                    <select class="form-select select2-tags @error('compatibility') is-invalid @enderror"
                                                            id="compatibility" name="compatibility" data-placeholder="Rechercher ou saisir une compatibilité...">
                                                        <option value="">{{ old('compatibility') ? '' : 'Rechercher ou saisir...' }}</option>
                                                        @if(old('compatibility') && !in_array(old('compatibility'), $compatibilities->toArray()))
                                                            <option value="{{ old('compatibility') }}" selected>{{ old('compatibility') }}</option>
                                                        @endif
                                                        @foreach($compatibilities as $compatibility)
                                                            <option value="{{ $compatibility }}" {{ old('compatibility') == $compatibility ? 'selected' : '' }}>{{ $compatibility }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('compatibility')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Pour accessoires - Recherchez ou saisissez une nouvelle compatibilité</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Prix et Stock -->
                                <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-dollar-sign me-2"></i>Prix et Stock</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Prix de vente <span id="price-required" class="text-danger">*</span></label>
                                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                                           id="price" name="price" value="{{ old('price') }}">
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Optionnel si statut = Brouillon</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cost_price" class="form-label">Prix de revient</label>
                                                    <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror"
                                                           id="cost_price" name="cost_price" value="{{ old('cost_price') }}">
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="stock_quantity" class="form-label">Quantité en stock <span id="stock-required" class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}">
                                                    @error('stock_quantity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Optionnel si statut = Brouillon</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attributs du Produit -->
                                <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Attributs du Produit</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="attributes-container">
                                            <p class="text-muted">Sélectionnez d'abord un type de produit pour voir les attributs disponibles.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Configuration -->
                                <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-cog me-2"></i>Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="barcode" class="form-label">Code-barres</label>
                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                                   id="barcode" name="barcode" value="{{ old('barcode') }}">
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="min_stock_alert" class="form-label">Alerte stock minimum</label>
                                            <input type="number" class="form-control @error('min_stock_alert') is-invalid @enderror"
                                                   id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', 10) }}">
                                            @error('min_stock_alert')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Statut *</label>
                                            <select class="form-select @error('status') is-invalid @enderror"
                                                    id="status" name="status" required>
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Actif</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Brouillon : permet de créer un produit incomplet</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="card mb-3 shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-images me-2"></i>Images</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="images" class="form-label">Images du produit</label>
                                            <input type="file" class="form-control @error('images') is-invalid @enderror"
                                                   id="images" name="images[]" multiple accept="image/*">
                                            @error('images')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Vous pouvez sélectionner plusieurs images (max 5)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tags et SEO -->
                                <div class="card shadow-sm border-0" style="border-radius: 10px;">
                                    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%); border-radius: 10px 10px 0 0;">
                                        <h5 class="mb-0 fw-bold"><i class="fas fa-tags me-2"></i>Tags et SEO</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="tags" class="form-label">Tags</label>
                                            <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                                   id="tags" name="tags" value="{{ old('tags') }}"
                                                   placeholder="tag1, tag2, tag3">
                                            @error('tags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Séparez les tags par des virgules</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Titre SEO (Meta Title)</label>
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                                   id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                                   maxlength="255" placeholder="Titre pour les moteurs de recherche">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel - Pour le référencement</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Description SEO (Meta Description)</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                                      id="meta_description" name="meta_description" rows="3"
                                                      maxlength="500" placeholder="Description pour les moteurs de recherche">{{ old('meta_description') }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel - Pour le référencement (max 500 caractères)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success" id="submitBtn" style="border-radius: 8px; padding: 10px 30px;">
                                        <i class="fas fa-save me-2"></i><span id="submitText">Créer le produit</span>
                                        <span id="submitSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery (requis pour Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialiser Select2 pour les champs avec tags (recherche + saisie manuelle)
    $('.select2-tags').select2({
        theme: 'bootstrap-5',
        tags: true,
        allowClear: true,
        placeholder: function() {
            return $(this).data('placeholder') || 'Rechercher ou saisir...';
        },
        language: {
            noResults: function() {
                return "Aucun résultat trouvé. Appuyez sur Entrée pour ajouter.";
            },
            searching: function() {
                return "Recherche en cours...";
            }
        },
        createTag: function (params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });

    // Gestion de la validation dynamique selon le statut
    const statusSelect = document.getElementById('status');
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock_quantity');
    const priceRequired = document.getElementById('price-required');
    const stockRequired = document.getElementById('stock-required');

    function updateRequiredFields() {
        const status = statusSelect.value;
        if (status === 'draft') {
            priceInput.removeAttribute('required');
            stockInput.removeAttribute('required');
            if (priceRequired) priceRequired.style.display = 'none';
            if (stockRequired) stockRequired.style.display = 'none';
        } else {
            priceInput.setAttribute('required', 'required');
            stockInput.setAttribute('required', 'required');
            if (priceRequired) priceRequired.style.display = 'inline';
            if (stockRequired) stockRequired.style.display = 'inline';
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', updateRequiredFields);
        updateRequiredFields(); // Initialiser au chargement
    }

    // Gestion des attributs du produit
    const productTypeSelect = document.getElementById('product_type_id');
    const attributesContainer = document.getElementById('attributes-container');
    const allAttributes = @json($attributes);

    function loadAttributesForProductType(productTypeId) {
        if (!productTypeId || !attributesContainer) {
            if (attributesContainer) {
                attributesContainer.innerHTML = '<p class="text-muted">Sélectionnez d\'abord un type de produit pour voir les attributs disponibles.</p>';
            }
            return;
        }

        // Afficher TOUS les attributs disponibles comme optionnels
        let html = '<div class="row">';

        // Afficher tous les attributs disponibles
        allAttributes.forEach((attribute, index) => {
            html += `
                <div class="col-md-6 mb-3">
                    <label for="attribute_${attribute.id}" class="form-label">
                        ${attribute.name}
                        <small class="text-muted">(optionnel)</small>
                    </label>
                    <input type="hidden" name="attributes[${index}][attribute_id]" value="${attribute.id}">
            `;

            if (attribute.type === 'select' && attribute.options) {
                html += `<select class="form-select" name="attributes[${index}][value]" id="attribute_${attribute.id}">
                    <option value="">Sélectionner... (optionnel)</option>`;
                attribute.options.forEach(option => {
                    html += `<option value="${option}">${option}</option>`;
                });
                html += '</select>';
            } else if (attribute.type === 'boolean') {
                html += `<select class="form-select" name="attributes[${index}][value]" id="attribute_${attribute.id}">
                    <option value="">Sélectionner... (optionnel)</option>
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                </select>`;
            } else {
                html += `<input type="${attribute.type === 'number' ? 'number' : 'text'}"
                         class="form-control"
                         name="attributes[${index}][value]"
                         id="attribute_${attribute.id}"
                         placeholder="Optionnel">`;
            }

            html += '</div></div>';
        });

        // Message informatif
        html += `
            <div class="col-12">
                <div class="alert alert-success">
                    <i class="fas fa-info-circle"></i>
                    <strong>Tous les attributs sont optionnels.</strong>
                    Vous pouvez laisser des champs vides et les remplir plus tard si nécessaire.
                </div>
            </div>
        `;

        html += '</div>';
        attributesContainer.innerHTML = html;
    }

    if (productTypeSelect) {
        productTypeSelect.addEventListener('change', function() {
            loadAttributesForProductType(this.value);
        });

        // Charger les attributs si un type est déjà sélectionné
        if (productTypeSelect.value) {
            loadAttributesForProductType(productTypeSelect.value);
        }
    }

    // Gestion de la soumission du formulaire
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');

    if (productForm && submitBtn) {
        productForm.addEventListener('submit', function(e) {
            // Désactiver le bouton pour éviter les doubles soumissions
            submitBtn.disabled = true;
            submitText.textContent = 'Création en cours...';
            submitSpinner.classList.remove('d-none');
            
            // Laisser le formulaire se soumettre normalement
            // Si une erreur se produit, le bouton sera réactivé par le rechargement de la page
        });
    }
});
</script>
@endsection




