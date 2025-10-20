@extends('admin.layouts.app')

@section('title', 'Variantes du Produit - ' . $product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Variantes de "{{ $product->name }}"</h3>
                            <small class="text-muted">Gérez les différentes variantes de ce produit</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nouvelle Variante
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour aux Produits
                            </a>
                        </div>
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

                    @if($variants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Nom</th>
                                        <th>SKU</th>
                                        <th>Attributs</th>
                                        <th>Prix</th>
                                        <th>Stock</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($variants as $variant)
                                    <tr>
                                        <td>
                                            @if($variant->images && count($variant->images) > 0)
                                                <img src="{{ Storage::url($variant->images[0]) }}"
                                                     alt="{{ $variant->variant_name }}"
                                                     class="product-image"
                                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px; border-radius: 8px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $variant->variant_name }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $variant->sku }}</code>
                                        </td>
                                        <td>
                                            @if($variant->attributes)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($variant->attributes as $attributeId => $value)
                                                        <span class="badge bg-light text-dark">{{ $value }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">Aucun attribut</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($variant->price && $variant->price != $product->price)
                                                <span class="text-success">{{ number_format($variant->price, 0, ',', ' ') }} FCFA</span>
                                                <br><small class="text-muted">(Produit: {{ number_format($product->price, 0, ',', ' ') }} FCFA)</small>
                                            @else
                                                <span class="text-muted">Prix du produit</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $variant->stock_quantity > 0 ? 'success' : 'warning' }}">
                                                {{ $variant->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.products.variants.toggle-status', [$product, $variant]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $variant->is_active ? 'success' : 'secondary' }}">
                                                    {{ $variant->is_active ? 'Actif' : 'Inactif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.variants.show', [$product, $variant]) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette variante ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune variante trouvée</h5>
                            <p class="text-muted">Ce produit n'a pas encore de variantes.</p>
                            <div class="mt-3">
                                <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Créer la première variante
                                </a>
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

