@extends('admin.layouts.app')

@section('title', 'Modifier le Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Produit: {{ $product->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured"
                                               value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">
                                            Produit en vedette
                                        </label>
                                    </div>
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
                                                         alt="{{ $image->alt_text ?: 'Image du produit' }}"
                                                         onerror="this.src='{{ asset('images/placeholder.svg') }}'">

                                                    <!-- Actions sur l'image -->
                                                    <div class="position-absolute top-0 end-0 p-1">
                                                        @if($image->type !== 'principale')
                                                            <form method="POST" action="{{ route('admin.products.set-main-image', [$product, $image->id]) }}" class="d-inline">
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
                                                        <form method="POST" action="{{ route('admin.products.delete-image', [$product, $image->id]) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?')"
                                                                    title="Supprimer l'image">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>

                                                    @if($image->type)
                                                        <div class="position-absolute top-0 start-0 p-1">
                                                            <span class="badge bg-{{ $image->type === 'principale' ? 'warning' : 'info' }}">
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
                                @elseif($product->images && count($product->images) > 0 && !empty(array_filter($product->images)))
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

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-génération du SKU basé sur le nom
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const sku = name.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '-')
            .substring(0, 20);
        document.getElementById('sku').value = sku;
    });

    // Validation côté client pour le stock quantity
    document.getElementById('stock_quantity').addEventListener('input', function() {
        const value = this.value;
        const regex = /^[1-9][0-9]*$/;

        if (value && !regex.test(value)) {
            this.setCustomValidity('La quantité doit être un nombre entier positif (ne peut pas commencer par 0)');
        } else {
            this.setCustomValidity('');
        }
    });

    // Gestion avancée des images avec prévisualisation
    document.getElementById('images').addEventListener('change', function() {
        const files = this.files;
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');

        // Vider la prévisualisation précédente
        previewContainer.innerHTML = '';
        imagePreview.style.display = 'none';

        if (files.length > 10) {
            alert('Vous ne pouvez sélectionner que 10 images maximum.');
            this.value = '';
            return;
        }

        let validFiles = [];
        let hasErrors = false;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            if (!allowedTypes.includes(file.type)) {
                alert(`Le fichier "${file.name}" n'est pas un format d'image valide. Formats acceptés: JPEG, PNG, JPG, GIF, WebP`);
                hasErrors = true;
                continue;
            }

            if (file.size > maxSize) {
                alert(`Le fichier "${file.name}" est trop volumineux. Taille maximale: 2MB`);
                hasErrors = true;
                continue;
            }

            validFiles.push(file);
        }

        if (hasErrors) {
            this.value = '';
            return;
        }

        // Afficher la prévisualisation
        if (validFiles.length > 0) {
            imagePreview.style.display = 'block';

            validFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6 mb-3';

                    col.innerHTML = `
                        <div class="image-preview-item position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 120px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 p-1">
                                <span class="badge bg-success">Nouveau</span>
                            </div>
                            <div class="text-center mt-1">
                                <small class="text-muted">${file.name}</small>
                                <br>
                                <small class="text-success">${(file.size / 1024).toFixed(1)} KB</small>
                            </div>
                        </div>
                    `;

                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Confirmation avant suppression d'image
    document.addEventListener('click', function(e) {
        if (e.target.closest('button[title="Supprimer l\'image"]')) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ? Cette action est irréversible.')) {
                e.preventDefault();
            }
        }
    });
</script>
@endpush
