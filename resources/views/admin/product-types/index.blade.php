@extends('admin.layouts.app')

@section('title', 'Gestion des Types de Produits')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Types de Produits</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Type de Produit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Statistiques -->
                    @if(isset($stats))
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['active'] }}</h3>
                                    <p class="mb-0">Actifs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $stats['inactive'] }}</h3>
                                    <p class="mb-0">Inactifs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Filtres -->
                    <form method="GET" action="{{ route('admin.product-types.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="">Toutes catégories</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="sort_by" class="form-control">
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                                    <option value="sort_order" {{ request('sort_by') == 'sort_order' ? 'selected' : '' }}>Ordre</option>
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="per_page" class="form-control">
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Description</th>
                                    <th>Attributs</th>
                                    <th>Produits</th>
                                    <th>Statut</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productTypes as $productType)
                                <tr>
                                    <td>
                                        <strong>{{ $productType->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $productType->slug }}</small>
                                    </td>
                                    <td>
                                        @if($productType->category)
                                            <span class="badge bg-info">
                                                @if($productType->category->icon)
                                                    <i class="{{ $productType->category->icon }}"></i>
                                                @endif
                                                {{ $productType->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucune catégorie</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($productType->description, 50) }}</td>
                                    <td>
                                        @if($productType->attributes->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($productType->attributes->take(3) as $attribute)
                                                    <span class="badge bg-light text-dark">
                                                        {{ $attribute->name }}
                                                        @if($attribute->pivot->is_required)
                                                            <i class="fas fa-asterisk text-danger" title="Requis"></i>
                                                        @endif
                                                    </span>
                                                @endforeach
                                                @if($productType->attributes->count() > 3)
                                                    <span class="badge bg-secondary">
                                                        +{{ $productType->attributes->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Aucun attribut</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $productType->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.product-types.toggle-status', $productType) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $productType->is_active ? 'success' : 'secondary' }}">
                                                {{ $productType->is_active ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $productType->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product-types.show', $productType) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product-types.destroy', $productType) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de produit ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-cube fa-3x mb-3"></i>
                                        <p>Aucun type de produit trouvé</p>
                                        <a href="{{ route('admin.product-types.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Créer le premier type de produit
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($productTypes) && $productTypes->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $productTypes->firstItem() }} à {{ $productTypes->lastItem() }} sur {{ $productTypes->total() }} résultats
                        </div>
                        <div>
                            {{ $productTypes->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-1 > * + * {
    margin-left: 0.25rem;
}
</style>
@endsection




