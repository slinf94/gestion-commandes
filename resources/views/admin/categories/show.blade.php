@extends('admin.layouts.app')

@section('title', 'Détails de la Catégorie - ' . $category->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Détails de la Catégorie</h3>
                            <small class="text-muted">{{ $category->name }}</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Nom :</th>
                                    <td>{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug :</th>
                                    <td><code>{{ $category->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Description :</th>
                                    <td>{{ $category->description ?: 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <th>Catégorie Parent :</th>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-info">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="text-muted">Catégorie principale</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut :</th>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Vedette :</th>
                                    <td>
                                        @if($category->is_featured)
                                            <span class="badge bg-warning">Produit vedette</span>
                                        @else
                                            <span class="text-muted">Non</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ordre :</th>
                                    <td>{{ $category->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>Icône :</th>
                                    <td>
                                        @if($category->icon)
                                            <i class="{{ $category->icon }}"></i> {{ $category->icon }}
                                        @else
                                            <span class="text-muted">Aucune icône</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Couleur :</th>
                                    <td>
                                        @if($category->color)
                                            <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                                {{ $category->color }}
                                            </span>
                                        @else
                                            <span class="text-muted">Couleur par défaut</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créée le :</th>
                                    <td>{{ $category->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Modifiée le :</th>
                                    <td>{{ $category->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            @if($category->image)
                                <div class="text-center">
                                    <img src="{{ Storage::url($category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="img-fluid rounded"
                                         style="max-height: 300px;">
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-3x mb-3"></i>
                                    <p>Aucune image</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sous-catégories -->
            @if($category->children->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sous-catégories ({{ $category->children->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Slug</th>
                                    <th>Statut</th>
                                    <th>Produits</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->children as $child)
                                <tr>
                                    <td>{{ $child->name }}</td>
                                    <td><code>{{ $child->slug }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $child->is_active ? 'success' : 'secondary' }}">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $child->products->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Produits de cette catégorie -->
            @if($category->products->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produits de cette catégorie ({{ $category->products->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->products->take(10) as $product)
                                <tr>
                                    <td>
                                        @if($product->productImages && $product->productImages->count() > 0)
                                            <img src="{{ Storage::url($product->productImages->first()->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="product-image"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; border-radius: 8px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_quantity > 0 ? 'success' : 'warning' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst(is_object($product->status) ? $product->status->value : $product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($category->products->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="btn btn-primary">
                                Voir tous les produits ({{ $category->products->count() }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
</style>
@endsection




