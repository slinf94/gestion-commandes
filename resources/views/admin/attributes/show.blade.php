@extends('admin.layouts.app')

@section('title', 'Détails de l\'Attribut - Allo Mobile Admin')
@section('page-title', 'Détails de l\'Attribut')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-tag me-2"></i>
                    {{ $attribute->name }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informations Générales</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom:</strong></td>
                                <td>{{ $attribute->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug:</strong></td>
                                <td><code>{{ $attribute->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Type:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $attribute->type == 'text' ? 'primary' : ($attribute->type == 'select' ? 'success' : 'info') }}">
                                        {{ ucfirst($attribute->type) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ordre:</strong></td>
                                <td>{{ $attribute->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $attribute->is_active ? 'success' : 'secondary' }}">
                                        {{ $attribute->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Propriétés</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Requis:</strong></td>
                                <td>
                                    @if($attribute->is_required)
                                        <i class="fas fa-check-circle text-success"></i> Oui
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> Non
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Filtrable:</strong></td>
                                <td>
                                    @if($attribute->is_filterable)
                                        <i class="fas fa-check-circle text-success"></i> Oui
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> Non
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Variante:</strong></td>
                                <td>
                                    @if($attribute->is_variant)
                                        <i class="fas fa-check-circle text-success"></i> Oui
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> Non
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($attribute->options && count($attribute->options) > 0)
                <hr>
                <h6 class="text-muted">Options Disponibles</h6>
                <div class="row">
                    @foreach($attribute->options as $option)
                        <div class="col-md-3 mb-2">
                            <span class="badge bg-light text-dark border">{{ $option }}</span>
                        </div>
                    @endforeach
                </div>
                @endif

                @if($attribute->validation_rules)
                <hr>
                <h6 class="text-muted">Règles de Validation</h6>
                <pre class="bg-light p-3 rounded"><code>{{ json_encode($attribute->validation_rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                @endif

                <div class="mt-4">
                    <h6 class="text-muted">Dates</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Créé le:</strong></td>
                            <td>{{ $attribute->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Modifié le:</strong></td>
                            <td>{{ $attribute->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>

                    <form action="{{ route('admin.attributes.toggle-status', $attribute) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-{{ $attribute->is_active ? 'secondary' : 'success' }} w-100">
                            <i class="fas fa-toggle-{{ $attribute->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $attribute->is_active ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>

                    <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet attribut ?');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($attribute->productTypeAttributes && $attribute->productTypeAttributes->count() > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">Types de Produits Associés</h6>
            </div>
            <div class="card-body">
                @foreach($attribute->productTypeAttributes as $productTypeAttribute)
                    <div class="mb-2">
                        <strong>{{ $productTypeAttribute->productType->name ?? 'N/A' }}</strong>
                        <br>
                        <small class="text-muted">
                            @if($productTypeAttribute->is_required) Requis @endif
                            @if($productTypeAttribute->is_filterable) • Filtrable @endif
                            @if($productTypeAttribute->is_variant) • Variante @endif
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @php
            $productCount = DB::table('product_attribute_values')
                ->join('product_type_attributes', 'product_attribute_values.product_type_attribute_id', '=', 'product_type_attributes.id')
                ->where('product_type_attributes.attribute_id', $attribute->id)
                ->count();
        @endphp

        @if($productCount > 0)
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Utilisé par les Produits</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>{{ $productCount }}</strong> produits utilisent cet attribut
                </p>
                <small class="text-muted">
                    Cet attribut ne peut pas être supprimé car il est utilisé par des produits.
                </small>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
