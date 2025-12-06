@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Prix par Quantité</h1>
                    <p class="text-muted">
                        <strong>{{ $product->name }}</strong> - Prix de base: {{ number_format($product->price, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour au produit
                </a>
            </div>
        </div>
    </div>

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

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter un Palier de Prix</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.quantity-prices.store', $product->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Quantité Min <span class="text-danger">*</span></label>
                            <input type="number" name="min_quantity" class="form-control @error('min_quantity') is-invalid @enderror" 
                                   value="{{ old('min_quantity', 1) }}" min="1" required>
                            @error('min_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Quantité Max <small class="text-muted">(vide = illimité)</small></label>
                            <input type="number" name="max_quantity" class="form-control @error('max_quantity') is-invalid @enderror" 
                                   value="{{ old('max_quantity') }}" min="1">
                            @error('max_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Prix Unitaire (FCFA) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Remise (%)</label>
                            <input type="number" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                   value="{{ old('discount_percentage', 0) }}" min="0" max="100" step="0.01">
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des prix existants -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Paliers de Prix Actuels</h5>
        </div>
        <div class="card-body">
            @if($product->prices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Plage de Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Remise</th>
                            <th>Prix Final</th>
                            <th>Économie</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->prices->sortBy('min_quantity') as $price)
                        <tr>
                            <td>
                                <strong>
                                    {{ $price->min_quantity }}
                                    @if($price->max_quantity)
                                        - {{ $price->max_quantity }}
                                    @else
                                        <i class="fas fa-infinity"></i>
                                    @endif
                                    unités
                                </strong>
                            </td>
                            <td>{{ number_format($price->price, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($price->discount_percentage > 0)
                                    <span class="badge bg-success">-{{ $price->discount_percentage }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($price->discounted_price, 0, ',', ' ') }} FCFA</strong>
                            </td>
                            <td>
                                @php
                                    $savings = (($product->price - $price->discounted_price) / $product->price) * 100;
                                @endphp
                                @if($savings > 0)
                                    <span class="badge bg-info">-{{ number_format($savings, 1) }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($price->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.products.quantity-prices.toggle', [$product->id, $price->id]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-{{ $price->is_active ? 'warning' : 'success' }}" 
                                            title="{{ $price->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $price->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.quantity-prices.destroy', [$product->id, $price->id]) }}" 
                                      method="POST" class="d-inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce palier de prix ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aucun palier de prix défini. Ajoutez des paliers pour proposer des tarifs dégressifs selon la quantité (comme Alibaba).
            </div>
            @endif
        </div>
    </div>

    <!-- Aide -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Comment ça marche ?</h5>
        </div>
        <div class="card-body">
            <h6>Prix dégressifs par quantité (style Alibaba) :</h6>
            <ul>
                <li><strong>Quantité Min :</strong> Quantité minimum pour bénéficier de ce prix</li>
                <li><strong>Quantité Max :</strong> Quantité maximum (laissez vide pour illimité)</li>
                <li><strong>Prix Unitaire :</strong> Prix par unité pour cette tranche</li>
                <li><strong>Remise :</strong> Pourcentage de réduction appliqué au prix unitaire</li>
            </ul>
            
            <h6 class="mt-3">Exemple :</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Économie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 - 9</td>
                            <td>1000 FCFA</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>10 - 49</td>
                            <td>900 FCFA</td>
                            <td>-10%</td>
                        </tr>
                        <tr>
                            <td>50+</td>
                            <td>800 FCFA</td>
                            <td>-20%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    font-weight: 600;
}
.badge {
    font-size: 0.85em;
}
</style>
@endsection
