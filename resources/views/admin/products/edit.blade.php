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
                                    <input type="file" class="form-control @error('images') is-invalid @enderror"
                                           id="images" name="images[]" multiple accept="image/*">
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Vous pouvez sélectionner plusieurs images</small>
                                </div>

                                @if($product->images && count($product->images) > 0 && !empty(array_filter($product->images)))
                                <div class="form-group">
                                    <label>Images actuelles</label>
                                    <div class="row">
                                        @foreach($product->images as $image)
                                            @if(is_string($image) && !empty($image))
                                            <div class="col-6 mb-2">
                                                <img src="{{ $image }}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                                            </div>
                                            @endif
                                        @endforeach
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
