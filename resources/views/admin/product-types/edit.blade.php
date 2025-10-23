@extends('admin.layouts.app')

@section('title', 'Modifier le Type de Produit - Allo Mobile Admin')
@section('page-title', 'Modifier le Type de Produit')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modifier le Type: {{ $productType->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.product-types.update', $productType) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du Type de Produit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $productType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $productType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                        {{ old('category_id', $productType->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordre de Tri</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $productType->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror"
                                           id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $productType->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Actif</strong>
                                    </label>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attributs Associés</label>
                        <div class="row">
                            @foreach($attributes as $attribute)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                               id="attribute_{{ $attribute->id }}"
                                               name="selected_attributes[]"
                                               value="{{ $attribute->id }}"
                                               {{ in_array($attribute->id, old('selected_attributes', $productType->attributes->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attribute_{{ $attribute->id }}">
                                            {{ $attribute->name }}
                                            <small class="text-muted d-block">({{ ucfirst($attribute->type) }})</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('selected_attributes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Informations</h6>
            </div>
            <div class="card-body">
                <h6>Attributs Actuels</h6>
                @if($productType->attributes->count() > 0)
                    <ul class="list-unstyled">
                        @foreach($productType->attributes as $attribute)
                            <li class="mb-2">
                                <strong>{{ $attribute->name }}</strong>
                                <br>
                                <small class="text-muted">{{ ucfirst($attribute->type) }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Aucun attribut associé</p>
                @endif

                <hr>

                <h6>Produits de ce Type</h6>
                <p class="mb-0">
                    <strong>{{ $productType->products->count() }}</strong> produits utilisent ce type
                </p>
            </div>
        </div>
    </div>
</div>
@endsection




