@extends('admin.layouts.app')

@section('title', 'Import/Export des Produits - Allo Mobile Admin')
@section('page-title', 'Import/Export des Produits')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $totalProducts }}</h4>
                                <p class="mb-0">Total Produits</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-boxes fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $activeProducts }}</h4>
                                <p class="mb-0">Produits Actifs</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $categories->count() }}</h4>
                                <p class="mb-0">Catégories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-folder fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $productTypes->count() }}</h4>
                                <p class="mb-0">Types de Produits</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-cube fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-download me-2"></i>Export des Produits
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.export.csv') }}" method="GET">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Filtrer par Catégorie</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="product_type_id" class="form-label">Filtrer par Type</label>
                        <select class="form-select" id="product_type_id" name="product_type_id">
                            <option value="">Tous les types</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Filtrer par Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="is_featured" class="form-label">Filtrer par Vedette</label>
                        <select class="form-select" id="is_featured" name="is_featured">
                            <option value="">Tous</option>
                            <option value="1">Vedette</option>
                            <option value="0">Non vedette</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Exporter en CSV
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Import -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-upload me-2"></i>Import des Produits
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.import.csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Fichier CSV</label>
                        <input type="file" class="form-control @error('csv_file') is-invalid @enderror"
                               id="csv_file" name="csv_file" accept=".csv,.txt" required>
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format CSV accepté (max 10MB)</div>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('admin.products.template.csv') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-2"></i>Télécharger le modèle CSV
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Importer les Produits
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Actions en lot -->
    <div class="col-md-12 mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>Actions en Lot
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('admin.products.bulk-update') }}" method="POST" id="bulkForm">
                            @csrf
                            <input type="hidden" name="product_ids" id="product_ids">
                            <input type="hidden" name="action" id="action">

                            <div class="mb-3">
                                <label for="bulk_category_id" class="form-label">Changer de Catégorie</label>
                                <select class="form-select" id="bulk_category_id" name="category_id">
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-success" onclick="bulkAction('activate')">
                                    <i class="fas fa-check me-2"></i>Activer
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="bulkAction('deactivate')">
                                    <i class="fas fa-times me-2"></i>Désactiver
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="bulkAction('change_category')">
                                    <i class="fas fa-folder me-2"></i>Changer Catégorie
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column">
                            <a href="{{ route('admin.products.statistics.export') }}" class="btn btn-outline-secondary mb-2">
                                <i class="fas fa-chart-bar me-2"></i>Exporter les Statistiques
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>Retour à la Liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection

@section('scripts')
<script>
function bulkAction(action) {
    const selectedProducts = getSelectedProducts(); // Fonction à implémenter selon votre interface

    if (selectedProducts.length === 0) {
        alert('Veuillez sélectionner au moins un produit.');
        return;
    }

    if (action === 'change_category' && !document.getElementById('bulk_category_id').value) {
        alert('Veuillez sélectionner une catégorie.');
        return;
    }

    if (action === 'delete' && !confirm('Êtes-vous sûr de vouloir supprimer les produits sélectionnés ?')) {
        return;
    }

    document.getElementById('product_ids').value = JSON.stringify(selectedProducts);
    document.getElementById('action').value = action;
    document.getElementById('bulkForm').submit();
}

function getSelectedProducts() {
    // Cette fonction doit récupérer les IDs des produits sélectionnés
    // Pour l'instant, retournons un tableau vide
    // À implémenter selon votre interface de sélection
    return [];
}
</script>
@endsection
