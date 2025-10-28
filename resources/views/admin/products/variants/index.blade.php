@extends('admin.layouts.app')

@section('title', 'Variantes du Produit')
@section('page-title', 'Variantes du Produit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Variantes de "{{ $product->name }}"</h4>
        <small class="text-muted">Gestion des variantes et options du produit</small>
    </div>
    <div>
        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left"></i> Retour au produit
        </a>
        <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-secondary">
            <i class="fas fa-plus"></i> Nouvelle variante
        </a>
    </div>
</div>

@if($variants->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom de la variante</th>
                            <th>SKU</th>
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
                                    <img src="{{ asset('storage/' . $variant->images[0]) }}"
                                         alt="{{ $variant->variant_name }}"
                                         class="img-thumbnail"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $variant->variant_name }}</strong>
                                @if($variant->attributes)
                                    <br><small class="text-muted">{{ $variant->attributes }}</small>
                                @endif
                            </td>
                            <td><code>{{ $variant->sku }}</code></td>
                            <td>{{ number_format($variant->price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span class="badge bg-{{ $variant->stock_quantity > 0 ? 'success' : 'danger' }}">
                                    {{ $variant->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $variant->is_active ? 'success' : 'secondary' }}">
                                    {{ $variant->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.variants.show', [$product, $variant]) }}"
                                       class="btn btn-sm btn-outline-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
                                       class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                                          id="delete-variant-{{ $variant->id }}"
                                          class="d-inline delete-variant-form">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-variant-btn"
                                            data-form-id="delete-variant-{{ $variant->id }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune variante</h5>
            <p class="text-muted">Ce produit n'a pas encore de variantes.</p>
            <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-secondary">
                <i class="fas fa-plus"></i> Créer la première variante
            </a>
        </div>
    </div>
@endif
@endsection
