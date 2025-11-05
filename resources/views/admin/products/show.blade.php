@extends('admin.layouts.app')

@section('title', 'Détails du Produit - {{ $product->name }} | Allo Mobile Admin')
@section('page-title', 'Détails du Produit')

@section('content')
<!-- Header moderne avec gradient vert -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
                    <i class="fas fa-box-open me-2"></i>{{ $product->name }}
                </h3>
                <small class="opacity-75">Informations complètes et gestion des images</small>
            </div>
            <div>
                <a href="{{ route('admin.products.edit', $product->slug ?? $product->id) }}" class="btn btn-light me-2" style="border-radius: 8px;">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-info-circle me-2"></i>Informations Générales
                    </h5>
                </div>
                <div class="card-body">
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
                                            <td><strong>Type de Produit:</strong></td>
                                            <td>{{ $product->productType->name ?? 'Non défini' }}</td>
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
                                                <span class="badge badge-secondary">Non disponible</span>
                                            </td>
                                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-dollar-sign me-2"></i>Prix et Stock
                    </h5>
                </div>
                <div class="card-body">
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
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h5>
                </div>
                <div class="card-body">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            @endif

            @if($product->tags && is_array($product->tags) && count($product->tags) > 0)
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-tags me-2"></i>Tags
                    </h5>
                </div>
                <div class="card-body">
                                    <div>
                                        @foreach($product->tags as $tag)
                                            <span class="badge badge-info mr-1">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                </div>
            </div>
            @endif

            @if($product->meta_title || $product->meta_description)
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-search me-2"></i>SEO
                    </h5>
                </div>
                <div class="card-body">
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

            @if($product->attributeValues && is_countable($product->attributeValues) && count($product->attributeValues) > 0)
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-list-ul me-2"></i>Attributs du Produit
                    </h5>
                </div>
                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th><i class="fas fa-tag me-1"></i>Attribut</th>
                                                    <th><i class="fas fa-info-circle me-1"></i>Valeur</th>
                                                    <th><i class="fas fa-cog me-1"></i>Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->attributeValues as $attributeValue)
                                                <tr>
                                                    <td>
                                                        <strong><i class="fas fa-tag text-muted me-1"></i>{{ $attributeValue->attribute_name ?? 'Attribut inconnu' }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($attributeValue->attribute_type === 'boolean')
                                                            @php
                                                                $boolValue = is_string($attributeValue->attribute_value) ? filter_var($attributeValue->attribute_value, FILTER_VALIDATE_BOOLEAN) : (bool)$attributeValue->attribute_value;
                                                            @endphp
                                                            <span class="badge badge-{{ $boolValue ? 'success' : 'secondary' }}">
                                                                <i class="fas fa-{{ $boolValue ? 'check' : 'times' }}"></i>
                                                                {{ $boolValue ? 'Oui' : 'Non' }}
                                                            </span>
                                                        @elseif($attributeValue->attribute_type === 'select' || $attributeValue->attribute_type === 'multiselect')
                                                            @php
                                                                $options = json_decode($attributeValue->options ?? '[]', true);
                                                                $values = is_string($attributeValue->attribute_value) ? json_decode($attributeValue->attribute_value, true) : $attributeValue->attribute_value;

                                                                if (is_array($values) && is_array($options)) {
                                                                    $displayValues = array_map(function($val) use ($options) {
                                                                        return isset($options[$val]) ? $options[$val] : $val;
                                                                    }, $values);
                                                                    $displayText = implode(', ', $displayValues);
                                                                } elseif (is_array($options)) {
                                                                    $displayText = isset($options[$attributeValue->attribute_value]) ? $options[$attributeValue->attribute_value] : $attributeValue->attribute_value;
                                                                } else {
                                                                    $displayText = is_array($values) ? implode(', ', $values) : $attributeValue->attribute_value;
                                                                }
                                                            @endphp
                                                            <span class="badge badge-primary">
                                                                <i class="fas fa-list"></i> {{ $displayText }}
                                                            </span>
                                                        @elseif($attributeValue->attribute_type === 'number' || $attributeValue->attribute_type === 'decimal')
                                                            <span class="text-primary"><strong>{{ number_format((float)$attributeValue->attribute_value, 2, ',', ' ') }}</strong></span>
                                                        @else
                                                            <span class="text-dark">{{ $attributeValue->attribute_value }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $typeIcons = [
                                                                'text' => 'fa-font',
                                                                'number' => 'fa-hashtag',
                                                                'decimal' => 'fa-hashtag',
                                                                'boolean' => 'fa-toggle-on',
                                                                'select' => 'fa-list',
                                                                'multiselect' => 'fa-tasks',
                                                                'date' => 'fa-calendar',
                                                                'json' => 'fa-code'
                                                            ];
                                                            $icon = $typeIcons[$attributeValue->attribute_type ?? 'text'] ?? 'fa-tag';
                                                        @endphp
                                                        <span class="badge badge-info">
                                                            <i class="fas {{ $icon }}"></i> {{ ucfirst($attributeValue->attribute_type ?? 'text') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>Ce produit n'a pas encore d'attributs définis.
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-image me-2"></i>Images
                    </h5>
                </div>
                <div class="card-body">

                            <!-- Formulaire d'upload d'images supplémentaires -->
                            <div class="mb-3">
                                <form method="POST" action="{{ route('admin.products.upload-images', $product->id) }}" enctype="multipart/form-data" id="uploadForm">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="images" class="form-label">Ajouter des images</label>
                                        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*" required>
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Vous pouvez sélectionner plusieurs images (max 10)</small>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success" id="uploadBtn">
                                        <i class="fas fa-upload"></i> Ajouter
                                    </button>
                                </form>
                            </div>

                            {{-- Debug temporaire --}}
                            {{-- @dd('Product ID:', $product->id, 'ProductImages count:', $product->productImages->count(), 'ProductImages:', $product->productImages->toArray(), 'Images attribute:', $product->images) --}}

                            @if($product->productImages && $product->productImages->count() > 0 && $product->productImages->where('url', '!=', '')->count() > 0)
                                <div class="row">
                                    @foreach($product->productImages as $image)
                                        @if($image->url && !empty($image->url))
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
                                                     alt="{{ isset($image->alt_text) ? $image->alt_text : 'Image du produit' }}"
                                                     onerror="this.src='{{ asset('images/placeholder.svg') }}'">

                                                <!-- Actions sur l'image -->
                                                <div class="position-absolute top-0 end-0 p-2">
                                                    @if(!isset($image->type) || $image->type !== 'principale')
                                                        <form method="POST" action="{{ route('admin.products.set-main-image', [$product->id, $image->id]) }}" class="d-inline">
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

                                                    <form method="POST" action="{{ route('admin.products.delete-image', [$product->id, $image->id]) }}"
                                                          class="d-inline delete-image-form"
                                                          onsubmit="return false;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteWithConfirmation('{{ route('admin.products.delete-image', [$product->id, $image->id]) }}', 'Êtes-vous sûr de vouloir supprimer cette image ? Cette action est irréversible.', 'DELETE')"
                                                                title="Supprimer l'image">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            @if(isset($image->type) && $image->type)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-tag"></i> {{ ucfirst($image->type) }}
                                                </small>
                                            @endif
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($product->images && is_array($product->images) && count(array_filter($product->images, function($img) { return !empty($img) && (is_string($img) || (is_array($img) && !empty($img))); })) > 0)
                                <div class="row">
                                    @foreach($product->images as $image)
                                        @if(is_string($image) && !empty($image))
                                        <div class="col-12 mb-3">
                                            @php
                                                // Si c'est une URL complète (http/https), l'utiliser directement
                                                if (str_starts_with($image, 'http')) {
                                                    $imageUrl = $image;
                                                } else {
                                                    // Sinon, ajouter le chemin storage
                                                    $imageUrl = asset('storage/' . ltrim($image, '/'));
                                                }
                                            @endphp
                                            <img src="{{ $imageUrl }}"
                                                 class="img-thumbnail"
                                                 style="width: 100%; height: 200px; object-fit: cover;"
                                                 alt="Image du produit"
                                                 onerror="this.src='{{ asset('images/placeholder.svg') }}'">
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

                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-chart-line me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                                <tr>
                                    <td><strong>Créé le:</strong></td>
                                    <td>{{ $product->created_at ? \Carbon\Carbon::parse($product->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Modifié le:</strong></td>
                                    <td>{{ $product->updated_at ? \Carbon\Carbon::parse($product->updated_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('images');
    const uploadBtn = document.getElementById('uploadBtn');

    if (uploadForm && fileInput && uploadBtn) {
        // Afficher le nombre de fichiers sélectionnés
        fileInput.addEventListener('change', function() {
            const files = this.files;
            console.log('Fichiers sélectionnés:', files.length);

            if (files.length > 0) {
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Ajouter (' + files.length + ' fichier' + (files.length > 1 ? 's' : '') + ')';
                uploadBtn.disabled = false;
            } else {
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Ajouter';
                uploadBtn.disabled = true;
            }
        });

        // Validation avant soumission
        uploadForm.addEventListener('submit', function(e) {
            const files = fileInput.files;

            if (files.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un fichier');
                return false;
            }

            if (files.length > 10) {
                e.preventDefault();
                alert('Vous ne pouvez pas uploader plus de 10 images à la fois');
                return false;
            }

            // Vérifier les types de fichiers
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];

                if (!validTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Le fichier "' + file.name + '" n\'est pas une image valide');
                    return false;
                }

                if (file.size > 2 * 1024 * 1024) { // 2MB
                    e.preventDefault();
                    alert('Le fichier "' + file.name + '" est trop volumineux (max 2MB)');
                    return false;
                }
            }

            // Désactiver le bouton pendant l'upload
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upload en cours...';

            console.log('Formulaire soumis avec', files.length, 'fichier(s)');
        });

        console.log('Script d\'upload initialisé');
    } else {
        console.error('Éléments du formulaire non trouvés');
    }
});
</script>
@endsection
