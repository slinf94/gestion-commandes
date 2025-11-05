{{-- 
    Composant de recherche avec autocomplete
    Usage: @include('admin.components.search-input', ['id' => 'search', 'name' => 'search', 'placeholder' => 'Rechercher...', 'searchUrl' => route('admin.search.api'), 'resultKey' => 'data'])
--}}
@php
    $id = $id ?? 'search';
    $name = $name ?? 'search';
    $placeholder = $placeholder ?? 'Rechercher...';
    $value = $value ?? request($name, '');
    $searchUrl = $searchUrl ?? '';
    $resultKey = $resultKey ?? 'data';
    $minLength = $minLength ?? 2;
    $debounceDelay = $debounceDelay ?? 500;
@endphp

<div class="search-autocomplete-wrapper position-relative">
    <label for="{{ $id }}" class="form-label">Recherche</label>
    <div class="position-relative">
        <input type="text" 
               class="form-control search-autocomplete-input" 
               id="{{ $id }}" 
               name="{{ $name }}" 
               placeholder="{{ $placeholder }}"
               value="{{ $value }}"
               autocomplete="off"
               data-search-url="{{ $searchUrl }}"
               data-result-key="{{ $resultKey }}"
               data-min-length="{{ $minLength }}"
               data-debounce-delay="{{ $debounceDelay }}"
               style="caret-color: #212529 !important; padding-right: {{ $searchUrl ? '60px' : '35px' }};">
        <div class="search-autocomplete-controls position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 5px;">
            <div class="search-autocomplete-spinner" style="display: none;">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <button type="button" 
                    class="search-autocomplete-clear btn btn-sm p-0" 
                    style="display: none; width: 20px; height: 20px; line-height: 1; border: none; background: transparent; color: #6c757d; cursor: pointer;"
                    title="Effacer la recherche">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div class="search-autocomplete-results position-absolute w-100" style="display: none; z-index: 1000; background: white; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-height: 400px; overflow-y: auto; margin-top: 5px;">
        <div class="search-autocomplete-list"></div>
        <div class="search-autocomplete-empty" style="display: none; padding: 20px; text-align: center; color: #6c757d;">
            <i class="fas fa-search" style="opacity: 0.3; font-size: 32px; margin-bottom: 10px;"></i>
            <p class="mb-0">Aucun résultat trouvé</p>
        </div>
    </div>
</div>

<style>
.search-autocomplete-wrapper {
    margin-bottom: 1rem;
}

.search-autocomplete-results {
    font-size: 0.9rem;
}

.search-autocomplete-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.search-autocomplete-item:hover {
    background-color: #f8f9fa;
}

.search-autocomplete-item:last-child {
    border-bottom: none;
}

.search-autocomplete-item.active {
    background-color: #e7f3ff;
}

.search-autocomplete-item-title {
    font-weight: 600;
    color: #212529;
    margin-bottom: 4px;
}

.search-autocomplete-item-subtitle {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 4px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.search-autocomplete-item-subtitle .badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.search-autocomplete-item-subtitle strong {
    color: #38B04A;
    font-weight: 600;
}

.search-autocomplete-input:focus {
    border-color: #38B04A;
    box-shadow: 0 0 0 0.2rem rgba(56, 176, 74, 0.25);
    caret-color: #212529 !important;
}

.search-autocomplete-input {
    caret-color: #212529 !important;
}

.search-autocomplete-clear {
    transition: all 0.2s ease;
}

.search-autocomplete-clear:hover {
    color: #dc3545 !important;
    transform: scale(1.1);
}

.search-autocomplete-controls {
    pointer-events: none;
}

.search-autocomplete-controls .search-autocomplete-spinner,
.search-autocomplete-controls .search-autocomplete-clear {
    pointer-events: auto;
}
</style>

