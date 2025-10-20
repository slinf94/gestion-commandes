@extends('admin.layouts.app')

@section('title', 'Créer une Variante - Allo Mobile Admin')
@section('page-title', 'Créer une Nouvelle Variante')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2"></i>
            Créer une Variante pour : {{ $product->name }}
        </h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="variant_name" class="form-label">Nom de la Variante <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('variant_name') is-invalid @enderror"
                               id="variant_name" name="variant_name" value="{{ old('variant_name') }}" required>
                        @error('variant_name')
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
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Prix <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price" value="{{ old('price') }}" step="0.01" required>
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
                               id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}" required>
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
                                            <option value="{{ $option }}" {{ old('attributes.'.$attribute->id) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif($attribute->type === 'text')
                                <input type="text" class="form-control" id="attribute_{{ $attribute->id }}"
                                       name="attributes[{{ $attribute->id }}]" value="{{ old('attributes.'.$attribute->id) }}">
                            @elseif($attribute->type === 'number')
                                <input type="number" class="form-control" id="attribute_{{ $attribute->id }}"
                                       name="attributes[{{ $attribute->id }}]" value="{{ old('attributes.'.$attribute->id) }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-3">
                <label for="images" class="form-label">Images de la Variante</label>
                <input type="file" class="form-control @error('images') is-invalid @enderror"
                       id="images" name="images[]" multiple accept="image/*">
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Vous pouvez sélectionner plusieurs images</small>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror"
                       id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Variante active</label>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Créer la Variante
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Générer automatiquement un SKU basé sur le nom de la variante
    document.getElementById('variant_name').addEventListener('input', function() {
        const variantName = this.value;
        const skuField = document.getElementById('sku');

        if (variantName && !skuField.value) {
            // Générer un SKU basé sur le nom de la variante
            const sku = variantName.toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 10) + '-' + Date.now().toString().slice(-4);
            skuField.value = sku;
        }
    });
</script>
@endsection

