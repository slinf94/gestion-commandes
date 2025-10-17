@extends('admin.layouts.app')

@section('title', 'Gestion des Produits - Allo Mobile Admin')
@section('page-title', 'Gestion des Produits')

@section('styles')
<style>
    .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
    .badge { font-size: 0.8em; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Liste des Produits</h4>
        <small class="text-muted">Gérez votre catalogue de produits</small>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nouveau Produit
    </a>
</div>

<div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Vedette</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        @if($product->mainImage)
                                            <img src="{{ $product->mainImage }}"
                                                 alt="{{ $product->name }}"
                                                 class="product-image"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="product-image bg-light d-flex align-items-center justify-content-center" style="display: none;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @else
                                            <div class="product-image bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->sku }}</small>
                                    </td>
                                    <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock_quantity > $product->min_stock_alert ? 'success' : 'warning' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->is_featured)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="fas fa-star text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($product->trashed())
                                                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Aucun produit trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

<div class="d-flex justify-content-center mt-4">
    {{ $products->links() }}
</div>
@endsection







