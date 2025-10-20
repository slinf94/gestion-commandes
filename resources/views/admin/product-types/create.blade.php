@extends('admin.layouts.app')

@section('title', 'Créer un Type de Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Créer un Nouveau Type de Produit</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.product-types.store') }}" method="POST" id="productTypeForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Informations Générales</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nom du type de produit *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

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

                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Attributs Associés</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted">Sélectionnez les attributs qui s'appliqueront à ce type de produit.</p>

                                        @if($attributes->count() > 0)
                                            <div class="row">
                                                @foreach($attributes as $attribute)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input attribute-checkbox"
                                                               id="attribute_{{ $attribute->id }}"
                                                               name="selected_attributes[]"
                                                               value="{{ $attribute->id }}"
                                                               {{ in_array($attribute->id, old('selected_attributes', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="attribute_{{ $attribute->id }}">
                                                            <strong>{{ $attribute->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                Type: {{ ucfirst($attribute->type) }}
                                                                @if($attribute->is_required)
                                                                    <span class="text-danger">*</span>
                                                                @endif
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                Aucun attribut disponible.
                                                <a href="{{ route('admin.attributes.create') }}" class="alert-link">
                                                    Créer des attributs
                                                </a> avant de continuer.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       id="is_active" name="is_active" value="1"
                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Type de produit actif
                                                </label>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Astuce :</strong> Les types de produits permettent de définir quels attributs s'appliquent aux produits de cette catégorie.
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Aperçu</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="preview-section" class="text-muted">
                                            <p><i class="fas fa-info-circle"></i> Remplissez le formulaire pour voir l'aperçu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary me-2">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Créer le type de produit
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
    const form = document.getElementById('productTypeForm');
    const previewSection = document.getElementById('preview-section');
    const nameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category_id');
    const attributeCheckboxes = document.querySelectorAll('.attribute-checkbox');

    function updatePreview() {
        const name = nameInput.value || 'Nouveau type de produit';
        const category = categorySelect.options[categorySelect.selectedIndex]?.text || 'Catégorie non sélectionnée';
        const selectedAttributes = Array.from(attributeCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.nextElementSibling.querySelector('strong').textContent);

        previewSection.innerHTML = `
            <div class="border rounded p-3">
                <h6><strong>${name}</strong></h6>
                <p class="mb-2"><strong>Catégorie:</strong> ${category}</p>
                <p class="mb-2"><strong>Attributs sélectionnés:</strong></p>
                ${selectedAttributes.length > 0
                    ? '<ul class="list-unstyled mb-0">' + selectedAttributes.map(attr => `<li><i class="fas fa-tag text-muted me-1"></i>${attr}</li>`).join('') + '</ul>'
                    : '<p class="text-muted mb-0">Aucun attribut sélectionné</p>'
                }
            </div>
        `;
    }

    nameInput.addEventListener('input', updatePreview);
    categorySelect.addEventListener('change', updatePreview);
    attributeCheckboxes.forEach(cb => cb.addEventListener('change', updatePreview));

    // Initial preview
    updatePreview();
});
</script>
@endsection

