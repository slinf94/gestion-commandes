@extends('admin.layouts.app')

@section('title', 'Nouvelle Variante')
@section('page-title', 'Nouvelle Variante')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Nouvelle variante pour "{{ $product->name }}"</h4>
        <small class="text-muted">Créer une nouvelle variante du produit</small>
    </div>
    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Retour aux variantes
    </a>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Informations de base</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="variant_name" class="form-label">Nom de la variante *</label>
                                        <input type="text" class="form-control @error('variant_name') is-invalid @enderror"
                                               id="variant_name" name="variant_name" value="{{ old('variant_name') }}"
                                               placeholder="Ex: Rouge, Taille L, etc.">
                                        @error('variant_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU *</label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                               id="sku" name="sku" value="{{ old('sku') }}"
                                               placeholder="Ex: PROD-001-RED-L">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="barcode" class="form-label">Code-barres</label>
                                        <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                               id="barcode" name="barcode" value="{{ old('barcode') }}"
                                               placeholder="Code-barres unique">
                                        @error('barcode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="attributes" class="form-label">Attributs</label>
                                        <textarea class="form-control @error('attributes') is-invalid @enderror"
                                                  id="attributes" name="attributes" rows="3"
                                                  placeholder="Ex: Couleur: Rouge, Taille: L">{{ old('attributes') }}</textarea>
                                        @error('attributes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Décrivez les caractéristiques spécifiques de cette variante</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Prix et stock</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Prix de vente *</label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                               id="price" name="price" value="{{ old('price') }}" step="0.01" min="0">
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="cost_price" class="form-label">Prix de revient</label>
                                        <input type="number" class="form-control @error('cost_price') is-invalid @enderror"
                                               id="cost_price" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0">
                                        @error('cost_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="wholesale_price" class="form-label">Prix de gros</label>
                                        <input type="number" class="form-control @error('wholesale_price') is-invalid @enderror"
                                               id="wholesale_price" name="wholesale_price" value="{{ old('wholesale_price') }}" step="0.01" min="0">
                                        @error('wholesale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="retail_price" class="form-label">Prix de détail</label>
                                        <input type="number" class="form-control @error('retail_price') is-invalid @enderror"
                                               id="retail_price" name="retail_price" value="{{ old('retail_price') }}" step="0.01" min="0">
                                        @error('retail_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">Quantité en stock *</label>
                                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0">
                                        @error('stock_quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="min_stock_alert" class="form-label">Alerte stock minimum</label>
                                        <input type="number" class="form-control @error('min_stock_alert') is-invalid @enderror"
                                               id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', 0) }}" min="0">
                                        @error('min_stock_alert')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Images de la variante</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Images</label>
                                        <input type="file" class="form-control @error('images') is-invalid @enderror"
                                               id="images" name="images[]" multiple accept="image/*">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Vous pouvez sélectionner plusieurs images (max 5)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-secondary me-2">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-save"></i> Créer la variante
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
