@extends('admin.layouts.app')

@section('title', 'Créer un Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Créer un Nouveau Produit</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Informations Générales -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Informations Générales</h5>
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
                                                    <label for="sku" class="form-label">SKU *</label>
                                                    <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                           id="sku" name="sku" value="{{ old('sku') }}" required>
                                                    @error('sku')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                    </div>
                                </div>

                                <!-- Prix et Stock -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Prix et Stock</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Prix de vente *</label>
                                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                                           id="price" name="price" value="{{ old('price') }}" required>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
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
                                                    <label for="stock_quantity" class="form-label">Quantité en stock *</label>
                                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
                                                    @error('stock_quantity')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attributs du Produit -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Attributs du Produit</h5>
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
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Configuration</h5>
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
                                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Actif</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5>Images</h5>
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

                                <!-- Tags -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Tags</h5>
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
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-save"></i> Créer le produit
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productTypeSelect = document.getElementById('product_type_id');
    const attributesContainer = document.getElementById('attributes-container');
    const allAttributes = @json($attributes);

    function loadAttributesForProductType(productTypeId) {
        if (!productTypeId) {
            attributesContainer.innerHTML = '<p class="text-muted">Sélectionnez d\'abord un type de produit pour voir les attributs disponibles.</p>';
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
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Tous les attributs sont optionnels.</strong>
                    Vous pouvez laisser des champs vides et les remplir plus tard si nécessaire.
                </div>
            </div>
        `;

        html += '</div>';
        attributesContainer.innerHTML = html;
    }

    productTypeSelect.addEventListener('change', function() {
        loadAttributesForProductType(this.value);
    });

    // Charger les attributs si un type est déjà sélectionné
    if (productTypeSelect.value) {
        loadAttributesForProductType(productTypeSelect.value);
    }
});
</script>
@endsection




