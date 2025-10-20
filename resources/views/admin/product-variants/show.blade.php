@extends('admin.layouts.app')

@section('title', 'Détails de la Variante - Allo Mobile Admin')
@section('page-title', 'Détails de la Variante')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Détails de la Variante : {{ $variant->variant_name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations Générales</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Nom :</strong></td>
                                <td>{{ $variant->variant_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>SKU :</strong></td>
                                <td>{{ $variant->sku ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Prix :</strong></td>
                                <td>{{ number_format($variant->price, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Stock :</strong></td>
                                <td>
                                    <span class="badge bg-{{ $variant->stock_quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $variant->stock_quantity }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Statut :</strong></td>
                                <td>
                                    <span class="badge bg-{{ $variant->is_active ? 'success' : 'secondary' }}">
                                        {{ $variant->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Produit Parent</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Nom :</strong></td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>SKU :</strong></td>
                                <td>{{ $product->sku }}</td>
                            </tr>
                            <tr>
                                <td><strong>Prix de base :</strong></td>
                                <td>{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($variant->attributes && is_array($variant->attributes))
                <div class="mt-4">
                    <h6>Attributs de la Variante</h6>
                    <div class="row">
                        @foreach($variant->attributes as $key => $value)
                        <div class="col-md-4 mb-2">
                            <div class="card bg-light">
                                <div class="card-body p-2">
                                    <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($variant->images && is_array($variant->images) && count($variant->images) > 0)
                <div class="mt-4">
                    <h6>Images de la Variante</h6>
                    <div class="row">
                        @foreach($variant->images as $image)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="{{ url('storage/' . $image) }}" class="card-img-top" alt="Image variante"
                                     style="height: 150px; object-fit: cover;">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mt-4">
                    <h6>Informations de Création</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Créé le :</strong></td>
                            <td>{{ $variant->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Modifié le :</strong></td>
                            <td>{{ $variant->updated_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>

                    <form method="POST" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette variante ?');" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.products.variants.toggle-status', [$product, $variant]) }}" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-{{ $variant->is_active ? 'secondary' : 'success' }}">
                            <i class="fas fa-toggle-{{ $variant->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $variant->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

