@extends('admin.layouts.app')

@section('title', 'Créer un Attribut - Allo Mobile Admin')
@section('page-title', 'Créer un Nouvel Attribut')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.attributes.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nom de l'Attribut <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Texte</option>
                    <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>Numérique</option>
                    <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Sélection simple</option>
                    <option value="multiselect" {{ old('type') == 'multiselect' ? 'selected' : '' }}>Sélection multiple</option>
                    <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Booléen</option>
                    <option value="date" {{ old('type') == 'date' ? 'selected' : '' }}>Date</option>
                    <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>Fichier</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="options-container" style="display: none;">
                <label for="options" class="form-label">Options</label>
                <div id="options-inputs">
                    <!-- Les champs d'options seront ajoutés dynamiquement -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-option">
                    <i class="fas fa-plus"></i> Ajouter une option
                </button>
                @error('options')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="sort_order" class="form-label">Ordre de Tri</label>
                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input @error('is_required') is-invalid @enderror" id="is_required" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_required">Requis</label>
                        @error('is_required')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input @error('is_filterable') is-invalid @enderror" id="is_filterable" name="is_filterable" value="1" {{ old('is_filterable', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_filterable">Filtrable</label>
                        @error('is_filterable')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input @error('is_variant') is-invalid @enderror" id="is_variant" name="is_variant" value="1" {{ old('is_variant') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_variant">Peut être une variante</label>
                        @error('is_variant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Actif</label>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Créer l'Attribut</button>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('options-container');
    const optionsInputs = document.getElementById('options-inputs');
    const addOptionBtn = document.getElementById('add-option');

    function toggleOptionsContainer() {
        const needsOptions = ['select', 'multiselect'].includes(typeSelect.value);
        optionsContainer.style.display = needsOptions ? 'block' : 'none';
    }

    function addOptionInput(value = '') {
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" class="form-control" name="options[]" value="${value}" placeholder="Option">
            <button type="button" class="btn btn-outline-danger remove-option">
                <i class="fas fa-times"></i>
            </button>
        `;
        optionsInputs.appendChild(div);
    }

    typeSelect.addEventListener('change', toggleOptionsContainer);

    addOptionBtn.addEventListener('click', function() {
        addOptionInput();
    });

    optionsInputs.addEventListener('click', function(e) {
        if (e.target.closest('.remove-option')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Initialiser si des options existent déjà
    @if(old('options'))
        @foreach(old('options') as $option)
            addOptionInput('{{ $option }}');
        @endforeach
    @endif
});
</script>
@endsection
