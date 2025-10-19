@extends('admin.layouts.app')

@section('title', 'Modifier la Variante - Allo Mobile Admin')
@section('page-title', 'Modifier la Variante')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>
            Modifier la Variante : {{ $variant->variant_name }}
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="variant_name" class="form-label">Nom de la Variante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('variant_name') is-invalid @enderror"
                               id="variant_name" name="variant_name" value="{{ old('variant_name', $variant->variant_name) }}" required>
                        @error('variant_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror"
                               id="sku" name="sku" value="{{ old('sku', $variant->sku) }}">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Prix <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price', $variant->price) }}" step="0.01" required>
                            <span class="input-group-text">FCFA</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">Quantité en Stock <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $variant->stock_quantity) }}" required>
                        @error('stock_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            @if($variantAttributes && $variantAttributes->count() > 0)
            <div class="mb-3">
                <label class="form-label">Attributs de Variante</label>
                <div class="card">
                    <div class="card-body">
                        @foreach($variantAttributes as $attribute)
                        <div class="mb-3">
                            <label for="attribute_{{ $attribute->id }}" class="form-label">{{ $attribute->name }}</label>
                            @if($attribute->type === 'select')
                                <select class="form-select" id="attribute_{{ $attribute->id }}" name="attributes[{{ $attribute->id }}]">
                                    <option value="">Sélectionner...</option>
                                    @if($attribute->options)
                                        @foreach($attribute->options as $option)
                                            <option value="{{ $option }}"
                                                {{ old('attributes.'.$attribute->id, $variant->attributes[$attribute->name] ?? '') == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif($attribute->type === 'text')
                                <input type="text" class="form-control" id="attribute_{{ $attribute->id }}"
                                       name="attributes[{{ $attribute->id }}]"
                                       value="{{ old('attributes.'.$attribute->id, $variant->attributes[$attribute->name] ?? '') }}">
                            @elseif($attribute->type === 'number')
                                <input type="number" class="form-control" id="attribute_{{ $attribute->id }}"
                                       name="attributes[{{ $attribute->id }}]"
                                       value="{{ old('attributes.'.$attribute->id, $variant->attributes[$attribute->name] ?? '') }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if($variant->images && is_array($variant->images) && count($variant->images) > 0)
            <div class="mb-3">
                <label class="form-label">Images Actuelles</label>
                <div class="row">
                    @foreach($variant->images as $index => $image)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="{{ url('storage/' . $image) }}" class="card-img-top" alt="Image variante"
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_images[]"
                                           value="{{ $index }}" id="remove_{{ $index }}">
                                    <label class="form-check-label text-danger" for="remove_{{ $index }}">
                                        Supprimer
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label for="images" class="form-label">Nouvelles Images</label>
                <input type="file" class="form-control @error('images') is-invalid @enderror"
                       id="images" name="images[]" multiple accept="image/*">
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Ajouter de nouvelles images (optionnel)</small>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror"
                       id="is_active" name="is_active" value="1"
                       {{ old('is_active', $variant->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Variante active</label>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.products.variants.show', [$product, $variant]) }}" class="btn btn-secondary">
                    <i class="fas fa-eye me-2"></i>Voir les Détails
                </a>
                <div>
                    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Mettre à Jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Avertir si des images vont être supprimées
    document.querySelectorAll('input[name="remove_images[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
                    this.checked = false;
                }
            }
        });
    });
</script>
@endsection
