@extends('admin.layouts.app')

@section('title', 'Modifier le Quartier - ' . $quartier->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Modifier le Quartier</h1>
                <div>
                    <a href="{{ route('admin.quartiers.show', $quartier) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informations du Quartier</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.quartiers.update', $quartier) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom">Nom du quartier *</label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                                   id="nom" name="nom" value="{{ old('nom', $quartier->nom) }}" required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ville">Ville *</label>
                                            <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                                   id="ville" name="ville" value="{{ old('ville', $quartier->ville) }}" required>
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
                                              placeholder="Description optionnelle du quartier...">{{ old('description', $quartier->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="latitude">Latitude</label>
                                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                                   id="latitude" name="latitude" value="{{ old('latitude', $quartier->latitude) }}"
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
                                                   id="longitude" name="longitude" value="{{ old('longitude', $quartier->longitude) }}"
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
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $quartier->sort_order) }}" min="0">
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
                                                       value="1" {{ old('is_active', $quartier->is_active) ? 'checked' : '' }}>
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
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                    <a href="{{ route('admin.quartiers.show', $quartier) }}" class="btn btn-secondary">
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
                            <h5 class="card-title mb-0">Statistiques</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary">{{ $quartier->clients_count }}</h4>
                                    <small class="text-muted">Total Clients</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $quartier->active_clients_count }}</h4>
                                    <small class="text-muted">Clients Actifs</small>
                                </div>
                            </div>

                            <hr>

                            <div class="text-center">
                                <p class="mb-2"><strong>Créé le :</strong></p>
                                <p class="text-muted">{{ $quartier->created_at->format('d/m/Y H:i') }}</p>

                                <p class="mb-2"><strong>Dernière modification :</strong></p>
                                <p class="text-muted">{{ $quartier->updated_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.quartiers.clients', $quartier) }}" class="btn btn-sm btn-primary btn-block">
                                    <i class="fas fa-users"></i> Voir les clients
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($quartier->clients_count == 0)
                                <form action="{{ route('admin.quartiers.destroy', $quartier) }}" method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce quartier ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash"></i> Supprimer le quartier
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <small>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Ce quartier ne peut pas être supprimé car il contient {{ $quartier->clients_count }} client(s).
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




