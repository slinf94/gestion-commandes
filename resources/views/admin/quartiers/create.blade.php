@extends('admin.layouts.app')

@section('title', 'Créer un Nouveau Quartier')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Créer un Nouveau Quartier</h1>
                <a href="{{ route('admin.quartiers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations du Quartier</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.quartiers.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom">Nom du quartier *</label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ville">Ville *</label>
                                            <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                                   id="ville" name="ville" value="{{ old('ville') }}" required>
                                            @error('ville')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Description optionnelle du quartier...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                                   id="latitude" name="latitude" value="{{ old('latitude') }}"
                                                   placeholder="Ex: 14.6934">
                                            @error('latitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Coordonnée GPS optionnelle</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="longitude">Longitude</label>
                                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                                   id="longitude" name="longitude" value="{{ old('longitude') }}"
                                                   placeholder="Ex: -17.4479">
                                            @error('longitude')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Coordonnée GPS optionnelle</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sort_order">Ordre d'affichage</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Pour ordonner l'affichage des quartiers</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check mt-4">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Quartier actif
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Les quartiers inactifs ne seront pas visibles</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Créer le quartier
                                    </button>
                                    <a href="{{ route('admin.quartiers.index') }}" class="btn btn-secondary">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aide</h5>
                        </div>
                        <div class="card-body">
                            <h6>Informations requises :</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Nom du quartier</li>
                                <li><i class="fas fa-check text-success"></i> Ville</li>
                            </ul>

                            <h6 class="mt-3">Informations optionnelles :</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info text-info"></i> Description</li>
                                <li><i class="fas fa-map-marker text-info"></i> Coordonnées GPS</li>
                                <li><i class="fas fa-sort text-info"></i> Ordre d'affichage</li>
                            </ul>

                            <div class="alert alert-info mt-3">
                                <small>
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Astuce :</strong> Les coordonnées GPS permettent de calculer les distances et d'optimiser les livraisons.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






















