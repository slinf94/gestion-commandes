@extends('admin.layouts.app')

@section('title', 'Gestion des Attributs - Allo Mobile Admin')
@section('page-title', 'Gestion des Attributs')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Liste des Attributs</h4>
                    <small class="text-muted">Gérez les attributs de vos produits (ex: Couleur, Taille, Marque)</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.attributes.create') }}" class="btn btn-secondary">
                        <i class="fas fa-plus me-2"></i>Nouvel Attribut
                    </a>
                    <button class="btn btn-outline-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter me-2"></i>Masquer Filtres
                    </button>
                </div>
            </div>

            <!-- Statistiques rapides -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $attributes->count() }}</h5>
                            <small>Total Attributs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $attributes->where('is_active', true)->count() }}</h5>
                            <small>Actifs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $attributes->where('is_variant', true)->count() }}</h5>
                            <small>Variantes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $attributes->where('is_filterable', true)->count() }}</h5>
                            <small>Filtrables</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres (TOUJOURS VISIBLES) -->
    <div class="row mb-4" id="filters-section">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.attributes.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="type_filter" class="form-label">Type</label>
                                <select name="type" id="type_filter" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Texte</option>
                                    <option value="number" {{ request('type') == 'number' ? 'selected' : '' }}>Nombre</option>
                                    <option value="select" {{ request('type') == 'select' ? 'selected' : '' }}>Sélection</option>
                                    <option value="multiselect" {{ request('type') == 'multiselect' ? 'selected' : '' }}>Multi-sélection</option>
                                    <option value="boolean" {{ request('type') == 'boolean' ? 'selected' : '' }}>Booléen</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_filter" class="form-label">Statut</label>
                                <select name="status" id="status_filter" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Nom ou slug..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tableau principal -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th width="20%">
                                        <i class="fas fa-tag me-1"></i>Nom
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            <i class="fas fa-sort ms-1"></i>
                                        </a>
                                    </th>
                                    <th width="12%">
                                        <i class="fas fa-code me-1"></i>Type
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'type', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            <i class="fas fa-sort ms-1"></i>
                                        </a>
                                    </th>
                                    <th width="20%">
                                        <i class="fas fa-list me-1"></i>Options
                                    </th>
                                    <th width="18%">
                                        <i class="fas fa-cogs me-1"></i>Propriétés
                                    </th>
                                    <th width="10%">
                                        <i class="fas fa-toggle-on me-1"></i>Statut
                                    </th>
                                    <th width="8%">
                                        <i class="fas fa-sort-numeric-up me-1"></i>Ordre
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'sort_order', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            <i class="fas fa-sort ms-1"></i>
                                        </a>
                                    </th>
                                    <th width="7%">
                                        <i class="fas fa-tools me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $attribute)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input attribute-checkbox" value="{{ $attribute->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="attribute-icon me-2">
                                                @if($attribute->type == 'select' || $attribute->type == 'multiselect')
                                                    <i class="fas fa-list text-success"></i>
                                                @elseif($attribute->type == 'number')
                                                    <i class="fas fa-hashtag text-info"></i>
                                                @elseif($attribute->type == 'boolean')
                                                    <i class="fas fa-toggle-on text-warning"></i>
                                                @else
                                                    <i class="fas fa-font text-primary"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <strong>{{ $attribute->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $attribute->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attribute->type == 'text' ? 'primary' : ($attribute->type == 'select' ? 'success' : ($attribute->type == 'number' ? 'info' : 'warning')) }} fs-6">
                                            {{ ucfirst($attribute->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attribute->type == 'select' || $attribute->type == 'multiselect')
                                            @if($attribute->options && count($attribute->options) > 0)
                                                <div class="options-container">
                                                    <small class="text-muted d-block mb-1">{{ count($attribute->options) }} option(s)</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($attribute->options, 0, 3) as $option)
                                                            <span class="badge bg-light text-dark">{{ $option }}</span>
                                                        @endforeach
                                                        @if(count($attribute->options) > 3)
                                                            <span class="badge bg-secondary">+{{ count($attribute->options) - 3 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Aucune option</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($attribute->is_required)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Requis
                                                </span>
                                            @endif
                                            @if($attribute->is_filterable)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-filter me-1"></i>Filtrable
                                                </span>
                                            @endif
                                            @if($attribute->is_variant)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-cube me-1"></i>Variante
                                                </span>
                                            @endif
                                            @if(!$attribute->is_required && !$attribute->is_filterable && !$attribute->is_variant)
                                                <span class="text-muted">Aucune propriété</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.attributes.toggle-status', $attribute) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $attribute->is_active ? 'success' : 'secondary' }}"
                                                    title="{{ $attribute->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $attribute->is_active ? 'check' : 'times' }}"></i>
                                                {{ $attribute->is_active ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $attribute->sort_order }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.attributes.show', $attribute) }}"
                                               class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.attributes.edit', $attribute) }}"
                                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST"
                                                  id="delete-attribute-{{ $attribute->id }}"
                                                  class="d-inline delete-attribute-form">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-attribute-btn" title="Supprimer"
                                                    data-form-id="delete-attribute-{{ $attribute->id }}"
                                                    data-attribute-name="{{ $attribute->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun attribut trouvé</h5>
                                            <p class="text-muted">Commencez par créer votre premier attribut pour organiser vos produits.</p>
                                            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Créer le premier attribut
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($attributes->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <small class="text-muted">
                                    Affichage de {{ $attributes->firstItem() ?? 0 }} à {{ $attributes->lastItem() ?? 0 }}
                                    sur {{ $attributes->total() }} attributs
                                </small>
                            </div>
                            <div>
                                {{ $attributes->links() }}
                            </div>
                        </div>
                    @endif

                    <!-- Actions en lot (masquées par défaut) -->
                    <div id="bulk-actions" class="mt-3" style="display: none;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">Actions sur la sélection :</span>
                            <button class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                                <i class="fas fa-check"></i> Activer
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                                <i class="fas fa-times"></i> Désactiver
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-1 > * + * {
    margin-left: 0.25rem;
}

.gap-2 > * + * {
    margin-left: 0.5rem;
}

.attribute-icon {
    width: 24px;
    text-align: center;
}

.options-container {
    max-width: 200px;
}

.empty-state {
    padding: 2rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
    border: none;
}

.table-light th {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

#filters-section .card {
    border: 1px solid #dee2e6;
}
</style>

<script>
function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    const button = event.target;

    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Masquer Filtres';
    } else {
        filtersSection.style.display = 'none';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Filtres';
    }
}

// Gestion de la sélection multiple
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const attributeCheckboxes = document.querySelectorAll('.attribute-checkbox');
    const bulkActions = document.getElementById('bulk-actions');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            attributeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkActions();
        });
    }

    attributeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.attribute-checkbox:checked');
            const allBoxes = document.querySelectorAll('.attribute-checkbox');

            selectAllCheckbox.checked = checkedBoxes.length === allBoxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < allBoxes.length;

            toggleBulkActions();
        });
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.attribute-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'block';
        } else {
            bulkActions.style.display = 'none';
        }
    }
});

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.attribute-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert('Veuillez sélectionner au moins un attribut.');
        return;
    }

    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = `Êtes-vous sûr de vouloir activer ${ids.length} attribut(s) ?`;
            break;
        case 'deactivate':
            confirmMessage = `Êtes-vous sûr de vouloir désactiver ${ids.length} attribut(s) ?`;
            break;
        case 'delete':
            confirmMessage = `Êtes-vous sûr de vouloir supprimer ${ids.length} attribut(s) ? Cette action est irréversible.`;
            break;
    }

    if (confirm(confirmMessage)) {
        // Ici vous pouvez ajouter la logique pour les actions en lot
        console.log('Action:', action, 'IDs:', ids);
        alert('Fonctionnalité d\'actions en lot à implémenter.');
    }
}

// Filtrage dynamique
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            input.addEventListener('change', function() {
                form.submit();
            });

            if (input.type === 'text') {
                input.addEventListener('keyup', function() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }
        });
    }

    // Gérer les boutons de suppression d'attributs
    const deleteButtons = document.querySelectorAll('.delete-attribute-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-form-id');
            const attributeName = this.getAttribute('data-attribute-name');
            const form = document.getElementById(formId);

            if (!form) return;

            customConfirm(
                `Êtes-vous sûr de vouloir supprimer l'attribut <strong>"${attributeName}"</strong> ? Cette action est irréversible.`,
                function() {
                    form.submit();
                },
                null,
                'Suppression d\'attribut',
                'Oui, supprimer',
                'Annuler'
            );
        });
    });
});
</script>
@endsection
