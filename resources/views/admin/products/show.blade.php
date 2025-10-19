@extends('admin.layouts.app')

@section('title', 'Détails du Produit - {{ $product->name }} | Allo Mobile Admin')
@section('page-title', 'Détails du Produit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">{{ $product->name }}</h4>
        <small class="text-muted">Informations complètes et gestion des images</small>
    </div>
    <div>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>
</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informations Générales</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nom:</strong></td>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>SKU:</strong></td>
                                            <td>{{ $product->sku }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Code-barres:</strong></td>
                                            <td>{{ $product->barcode ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Catégorie:</strong></td>
                                            <td>{{ $product->category->name ?? 'Non définie' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst(is_object($product->status) ? $product->status->value : $product->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>En vedette:</strong></td>
                                            <td>
                                                @if($product->is_featured)
                                                    <span class="badge badge-warning">Oui</span>
                                                @else
                                                    <span class="badge badge-secondary">Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Prix et Stock</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Prix de vente:</strong></td>
                                            <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prix de revient:</strong></td>
                                            <td>{{ $product->cost_price ? number_format($product->cost_price, 0, ',', ' ') . ' FCFA' : 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Quantité en stock:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $product->stock_quantity > $product->min_stock_alert ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alerte stock:</strong></td>
                                            <td>{{ $product->min_stock_alert }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marge:</strong></td>
                                            <td>
                                                @if($product->cost_price)
                                                    {{ number_format((($product->price - $product->cost_price) / $product->price) * 100, 1) }}%
                                                @else
                                                    Non calculable
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($product->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Description</h5>
                                    <div class="border p-3 rounded">
                                        {!! nl2br(e($product->description)) !!}
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($product->tags && is_array($product->tags) && count($product->tags) > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Tags</h5>
                                    <div>
                                        @foreach($product->tags as $tag)
                                            <span class="badge badge-info mr-1">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($product->meta_title || $product->meta_description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>SEO</h5>
                                    <table class="table table-borderless">
                                        @if($product->meta_title)
                                        <tr>
                                            <td><strong>Titre SEO:</strong></td>
                                            <td>{{ $product->meta_title }}</td>
                                        </tr>
                                        @endif
                                        @if($product->meta_description)
                                        <tr>
                                            <td><strong>Description SEO:</strong></td>
                                            <td>{{ $product->meta_description }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <h5>Images</h5>

                            <!-- Formulaire d'upload d'images supplémentaires -->
                            <div class="mb-3">
                                <form method="POST" action="{{ route('admin.products.upload-images', $product) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="images" class="form-label">Ajouter des images</label>
                                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">Vous pouvez sélectionner plusieurs images (max 10)</small>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-upload"></i> Ajouter
                                    </button>
                                </form>
                            </div>

                            @if($product->productImages && $product->productImages->count() > 0)
                                <div class="row">
                                    @foreach($product->productImages as $image)
                                        <div class="col-12 mb-3">
                                            @php
                                                $imageUrl = $image->url;
                                                // Si l'URL ne commence pas par http, ajouter le chemin storage
                                                if (!str_starts_with($imageUrl, 'http')) {
                                                    $imageUrl = asset('storage/' . ltrim($imageUrl, '/'));
                                                }
                                            @endphp
                                            <div class="position-relative">
                                                <img src="{{ $imageUrl }}"
                                                     class="img-thumbnail"
                                                     style="width: 100%; height: 200px; object-fit: cover;"
                                                     alt="{{ $image->alt_text ?: 'Image du produit' }}"
                                                     onerror="this.src='{{ asset('images/placeholder.svg') }}'">

                                                <!-- Actions sur l'image -->
                                                <div class="position-absolute top-0 end-0 p-2">
                                                    @if($image->type !== 'principale')
                                                        <form method="POST" action="{{ route('admin.products.set-main-image', [$product, $image]) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Définir comme image principale">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-star"></i> Principale
                                                        </span>
                                                    @endif

                                                    <form method="POST" action="{{ route('admin.products.delete-image', [$product, $image]) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?')"
                                                                title="Supprimer l'image">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            @if($image->type)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-tag"></i> {{ ucfirst($image->type) }}
                                                </small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($product->images && count($product->images) > 0 && !empty(array_filter($product->images)))
                                <div class="row">
                                    @foreach($product->images as $image)
                                        @if(is_string($image) && !empty($image))
                                        <div class="col-12 mb-3">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail" style="width: 100%; height: 200px; object-fit: cover;" alt="Image du produit">
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <p>Aucune image</p>
                                </div>
                            @endif

                            <h5 class="mt-4">Statistiques</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Créé le:</strong></td>
                                    <td>{{ $product->created_at ? $product->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Modifié le:</strong></td>
                                    <td>{{ $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
@endsection
