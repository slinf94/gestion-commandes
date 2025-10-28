@extends('admin.layouts.app')

@section('title', 'Modifier le Produit')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-dismiss" data-dismiss-time="3000" role="alert">
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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Erreur:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Header moderne avec gradient vert -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
                    <i class="fas fa-edit me-2"></i>Modifier: {{ $product->name }}
                </h3>
                <small class="opacity-75">Mettez à jour les informations du produit</small>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body" style="padding: 30px;">
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productEditForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Nom du Produit *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Prix *</label>
                                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                                                   id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cost_price">Prix de Revient</label>
                                            <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror"
                                                   id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                            @error('cost_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="stock_quantity">Quantité en Stock *</label>
                                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min_stock_alert">Alerte Stock Minimum</label>
                                            <input type="number" class="form-control @error('min_stock_alert') is-invalid @enderror"
                                                   id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}">
                                            @error('min_stock_alert')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sku">SKU *</label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="barcode">Code-barres</label>
                                            <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                                   id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                            @error('barcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="category_id">Catégorie *</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tags">Tags (séparés par des virgules)</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                           id="tags" name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}"
                                           placeholder="ex: smartphone, mobile, android">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="images">Nouvelles Images</label>
                                    <div class="image-upload-area">
                                        <input type="file" class="form-control @error('images') is-invalid @enderror"
                                               id="images" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Formats acceptés: JPEG, PNG, JPG, GIF, WebP (max 2MB par image, max 10 images)
                                        </small>

                                        <!-- Zone de prévisualisation des nouvelles images -->
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <h6><i class="fas fa-images"></i> Aperçu des nouvelles images :</h6>
                                            <div class="row" id="preview-container"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gestion des images existantes -->
                                @if($product->productImages && $product->productImages->count() > 0)
                                <div class="form-group">
                                    <label><i class="fas fa-images"></i> Images actuelles ({{ $product->productImages->count() }} image(s))</label>
                                    <div class="current-images-container">
                                        <div class="row">
                                            @foreach($product->productImages as $image)
                                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                @php
                                                    $imageUrl = $image->url;
                                                    if (!str_starts_with($imageUrl, 'http')) {
                                                        $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                                                    }
                                                @endphp
                                                <!-- Debug: Image ID {{ $image->id }}, URL: {{ $imageUrl }} -->
                                                <div class="image-item position-relative">
                                                    <img src="{{ $imageUrl }}"
                                                         class="img-thumbnail"
                                                         style="width: 100%; height: 120px; object-fit: cover;"
                                                         alt="{{ isset($image->alt_text) ? $image->alt_text : 'Image du produit' }}"
                                                         onerror="this.src='{{ asset('images/placeholder.svg') }}'">

                                                    <!-- Actions sur l'image -->
                                                    <div class="position-absolute top-0 end-0 p-1">
                                                        @if(!isset($image->type) || $image->type !== 'principale')
                                                            <form method="POST" action="{{ route('admin.products.set-main-image', [$product->id, $image->id]) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-warning" title="Définir comme image principale">
                                                                    <i class="fas fa-star"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-star"></i> Principale
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="position-absolute bottom-0 end-0 p-1">
                                                        <form method="POST" action="{{ route('admin.products.delete-image', [$product->id, $image->id]) }}"
                                                              class="d-inline delete-image-form"
                                                              onsubmit="return false;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger delete-image-btn"
                                                                    data-url="{{ route('admin.products.delete-image', [$product->id, $image->id]) }}"
                                                                    data-message="Êtes-vous sûr de vouloir supprimer cette image ? Cette action est irréversible."
                                                                    title="Supprimer l&#39;image">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    @if(isset($image->type) && $image->type)
                                                        <div class="position-absolute top-0 start-0 p-1">
                                                            <span class="badge bg-{{ isset($image->type) && $image->type === 'principale' ? 'warning' : 'info' }}">
                                                                {{ ucfirst($image->type) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @elseif(isset($product->images) && is_array($product->images) && count(array_filter($product->images, function($img) { return !empty($img) && (is_string($img) || (is_array($img) && !empty($img))); })) > 0)
                                <div class="form-group">
                                    <label><i class="fas fa-images"></i> Images actuelles (ancien système)</label>
                                    <div class="row">
                                        @foreach($product->images as $image)
                                            @if(is_string($image) && !empty($image))
                                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                     class="img-thumbnail"
                                                     style="width: 100%; height: 120px; object-fit: cover;"
                                                     alt="Image du produit">
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-info-circle"></i>
                                        Ces images utilisent l'ancien système. Elles seront automatiquement migrées vers le nouveau système lors de la prochaine modification.
                                    </div>
                                </div>
                                @else
                                <div class="form-group">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Aucune image n'est actuellement associée à ce produit.
                                    </div>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label for="meta_title">Titre SEO</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                           id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="meta_description">Description SEO</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $product->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    <div class="d-flex justify-content-between mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" id="updateButton" class="btn btn-success" style="border-radius: 8px; background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; padding: 10px 30px;">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Version SIMPLE - Pas de JavaScript complexe, juste la soumission du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Page chargée');

        const form = document.getElementById('productEditForm');
        const submitBtn = document.getElementById('updateButton');

        if (form && submitBtn) {
            // Quand on clique sur le bouton
            submitBtn.addEventListener('click', function(e) {
                console.log('🖱️ Bouton cliqué');
                e.preventDefault();

                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mise à jour en cours...';
                submitBtn.style.pointerEvents = 'none';

                // Soumettre le formulaire
                console.log('✅ Soumission du formulaire');
                form.submit();
            });
        }
    });
</script>
@endsection
