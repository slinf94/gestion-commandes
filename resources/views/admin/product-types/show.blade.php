@extends('admin.layouts.app')

@section('title', 'Détails du Type de Produit - ' . $productType->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Détails du Type de Produit</h3>
                            <small class="text-muted">{{ $productType->name }}</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.product-types.edit', $productType) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
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
                                    <td>{{ $productType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug :</th>
                                    <td><code>{{ $productType->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Description :</th>
                                    <td>{{ $productType->description ?: 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <th>Catégorie :</th>
                                    <td>
                                        @if($productType->category)
                                            <span class="badge bg-info">{{ $productType->category->name }}</span>
                                        @else
                                            <span class="text-muted">Aucune catégorie</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut :</th>
                                    <td>
                                        <span class="badge bg-{{ $productType->is_active ? 'success' : 'secondary' }}">
                                            {{ $productType->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ordre :</th>
                                    <td>{{ $productType->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>Créé le :</th>
                                    <td>{{ $productType->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Modifié le :</th>
                                    <td>{{ $productType->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Type de Produit</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attributs associés -->
            @if($productType->attributes->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attributs associés ({{ $productType->attributes->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Requis</th>
                                    <th>Filtrable</th>
                                    <th>Variant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productType->attributes as $attribute)
                                <tr>
                                    <td>{{ $attribute->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($attribute->type) }}</span>
                                    </td>
                                    <td>
                                        @if($attribute->pivot->is_required ?? false)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attribute->pivot->is_filterable ?? true)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attribute->pivot->is_variant ?? false)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.attributes.show', $attribute) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Produits de ce type -->
            @if($productType->products->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Produits de ce type ({{ $productType->products->count() }})</h5>
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
                                @foreach($productType->products->take(10) as $product)
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
                    @if($productType->products->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.products.index', ['product_type_id' => $productType->id]) }}" class="btn btn-primary">
                                Voir tous les produits ({{ $productType->products->count() }})
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
