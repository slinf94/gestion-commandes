@extends('admin.layouts.app')

@section('title', 'Modifier l\'Attribut - Allo Mobile Admin')
@section('page-title', 'Modifier l\'Attribut')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modifier l'Attribut: {{ $attribute->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom de l'Attribut <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="text" {{ old('type', $attribute->type) == 'text' ? 'selected' : '' }}>Texte</option>
                                    <option value="number" {{ old('type', $attribute->type) == 'number' ? 'selected' : '' }}>Nombre</option>
                                    <option value="select" {{ old('type', $attribute->type) == 'select' ? 'selected' : '' }}>Sélection</option>
                                    <option value="multiselect" {{ old('type', $attribute->type) == 'multiselect' ? 'selected' : '' }}>Sélection multiple</option>
                                    <option value="boolean" {{ old('type', $attribute->type) == 'boolean' ? 'selected' : '' }}>Booléen</option>
                                    <option value="date" {{ old('type', $attribute->type) == 'date' ? 'selected' : '' }}>Date</option>
                                    <option value="file" {{ old('type', $attribute->type) == 'file' ? 'selected' : '' }}>Fichier</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="options" class="form-label">Options (une par ligne)</label>
                        <textarea class="form-control @error('options') is-invalid @enderror"
                                  id="options" name="options" rows="5"
                                  placeholder="Entrez les options une par ligne...">{{ old('options', $attribute->options ? implode("\n", $attribute->options) : '') }}</textarea>
                        <div class="form-text">Laissez vide si le type n'est pas "select" ou "multiselect"</div>
                        @error('options')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordre de Tri</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $attribute->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="validation_rules" class="form-label">Règles de Validation (JSON)</label>
                                <textarea class="form-control @error('validation_rules') is-invalid @enderror"
                                          id="validation_rules" name="validation_rules" rows="3"
                                          placeholder='{"min": 1, "max": 100}'>{{ old('validation_rules', $attribute->validation_rules ? json_encode($attribute->validation_rules, JSON_PRETTY_PRINT) : '') }}</textarea>
                                @error('validation_rules')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_required') is-invalid @enderror"
                                       id="is_required" name="is_required" value="1"
                                       {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">
                                    <strong>Requis</strong>
                                </label>
                                @error('is_required')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_filterable') is-invalid @enderror"
                                       id="is_filterable" name="is_filterable" value="1"
                                       {{ old('is_filterable', $attribute->is_filterable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_filterable">
                                    <strong>Filtrable</strong>
                                </label>
                                @error('is_filterable')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input @error('is_variant') is-invalid @enderror"
                                       id="is_variant" name="is_variant" value="1"
                                       {{ old('is_variant', $attribute->is_variant) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_variant">
                                    <strong>Variante</strong>
                                </label>
                                @error('is_variant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror"
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Actif</strong>
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
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
                <h6>Types d'Attributs</h6>
                <ul class="list-unstyled">
                    <li><strong>Texte:</strong> Saisie libre</li>
                    <li><strong>Nombre:</strong> Valeur numérique</li>
                    <li><strong>Sélection:</strong> Choix unique</li>
                    <li><strong>Sélection multiple:</strong> Choix multiples</li>
                    <li><strong>Booléen:</strong> Oui/Non</li>
                    <li><strong>Date:</strong> Date</li>
                    <li><strong>Fichier:</strong> Upload de fichier</li>
                </ul>

                <hr>

                <h6>Propriétés</h6>
                <ul class="list-unstyled">
                    <li><strong>Requis:</strong> Obligatoire pour les produits</li>
                    <li><strong>Filtrable:</strong> Peut être utilisé pour filtrer</li>
                    <li><strong>Variante:</strong> Crée des variantes de produits</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const optionsTextarea = document.getElementById('options');

    function toggleOptions() {
        const showOptions = ['select', 'multiselect'].includes(typeSelect.value);
        optionsTextarea.style.display = showOptions ? 'block' : 'none';
        optionsTextarea.closest('.mb-3').style.display = showOptions ? 'block' : 'none';
    }

    typeSelect.addEventListener('change', toggleOptions);
    toggleOptions(); // Initial call
});
</script>
@endpush
@endsection

