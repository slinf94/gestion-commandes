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
    <div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Produit
        </a>
        <a href="{{ route('admin.products.import-export') }}" class="btn btn-success">
            <i class="fas fa-file-csv me-2"></i>Import/Export
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="search" name="search"
                       value="{{ request('search') }}" placeholder="Nom, SKU, description...">
            </div>
            <div class="col-md-2">
                <label for="category_id" class="form-label">Catégorie</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="product_type_id" class="form-label">Type de Produit</label>
                <select class="form-select" id="product_type_id" name="product_type_id">
                    <option value="">Tous les types</option>
                    @foreach($productTypes as $productType)
                        <option value="{{ $productType->id }}" {{ request('product_type_id') == $productType->id ? 'selected' : '' }}>
                            {{ $productType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Effacer
                </a>
            </div>
        </form>
    </div>
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
                                    <th>Type</th>
                                    <th>Statut</th>
                                    <th>Vedette</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        @if($product->productImages && $product->productImages->count() > 0 && $product->productImages->first()->url)
                                            @php
                                                $firstImage = $product->productImages->first();
                                                $imageUrl = asset('storage/' . ltrim($firstImage->url, '/'));
                                            @endphp
                                            <img src="{{ $imageUrl }}"
                                                 alt="{{ $product->name }}"
                                                 class="product-image"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="product-image bg-light d-flex align-items-center justify-content-center" style="display: none;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @elseif($product->images && is_array($product->images) && count(array_filter($product->images, function($img) { return !empty($img) && (is_string($img) || (is_array($img) && !empty($img))); })) > 0)
                                            @php
                                                $firstImage = $product->images[0];
                                                $imagePath = null;

                                                // Debug temporaire - décommentez pour voir les données
                                                // dd('Product ID: ' . $product->id, 'Images: ', $product->images, 'First Image: ', $firstImage, 'ImagePath: ', $imagePath);

                                                // Gestion robuste des différents formats d'images
                                                if (is_string($firstImage)) {
                                                    // Si c'est déjà une chaîne, l'utiliser directement
                                                    $imagePath = $firstImage;
                                                } elseif (is_object($firstImage)) {
                                                    // Si c'est un objet, chercher une propriété url ou path
                                                    $imagePath = $firstImage->url ?? $firstImage->path ?? $firstImage->filename ?? null;
                                                } elseif (is_array($firstImage)) {
                                                    // Si c'est un array, chercher une clé url ou path
                                                    $imagePath = $firstImage['url'] ?? $firstImage['path'] ?? $firstImage['filename'] ?? $firstImage[0] ?? null;
                                                }

                                                // S'assurer que $imagePath est une chaîne valide
                                                if (is_string($imagePath) && !empty($imagePath)) {
                                                    // Si c'est une URL complète (http/https), l'utiliser directement
                                                    if (str_starts_with($imagePath, 'http')) {
                                                        $imageUrl = $imagePath;
                                                    } else {
                                                        // Sinon, ajouter le chemin storage
                                                        $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                                                    }
                                                } else {
                                                    $imageUrl = null;
                                                }
                                            @endphp
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}"
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
                                        @if($product->productType)
                                            <span class="badge bg-info">{{ $product->productType->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst(is_object($product->status) ? $product->status->value : $product->status) }}
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
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn btn-sm btn-outline-info" title="Variantes">
                                                <i class="fas fa-cubes"></i>
                                            </a>
                                            @if($product->deleted_at)
                                                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="d-inline">
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







