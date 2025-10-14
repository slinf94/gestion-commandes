@extends('admin.layouts.app')

@section('title', 'Modifier le Profil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">Mon Profil</a></li>
    <li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le Profil
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom *</label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                           id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom *</label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror"
                                           id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_telephone">Numéro de téléphone</label>
                                    <input type="text" class="form-control @error('numero_telephone') is-invalid @enderror"
                                           id="numero_telephone" name="numero_telephone"
                                           value="{{ old('numero_telephone', $user->numero_telephone) }}"
                                           placeholder="+225 XX XX XX XX XX">
                                    @error('numero_telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_whatsapp">Numéro WhatsApp</label>
                                    <input type="text" class="form-control @error('numero_whatsapp') is-invalid @enderror"
                                           id="numero_whatsapp" name="numero_whatsapp"
                                           value="{{ old('numero_whatsapp', $user->numero_whatsapp) }}"
                                           placeholder="+225 XX XX XX XX XX">
                                    @error('numero_whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="localisation">Localisation</label>
                                    <input type="text" class="form-control @error('localisation') is-invalid @enderror"
                                           id="localisation" name="localisation"
                                           value="{{ old('localisation', $user->localisation) }}"
                                           placeholder="ex: Abidjan">
                                    @error('localisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quartier">Quartier</label>
                                    <input type="text" class="form-control @error('quartier') is-invalid @enderror"
                                           id="quartier" name="quartier"
                                           value="{{ old('quartier', $user->quartier) }}"
                                           placeholder="ex: Cocody">
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Mettre à jour
                            </button>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
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
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Conseil :</strong> Mettez à jour vos informations pour faciliter la communication.
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> La modification de l'email nécessitera une nouvelle vérification.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Sécurité
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Pour changer votre mot de passe, utilisez le bouton ci-dessous :</p>
                    <a href="{{ route('admin.profile.password') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-key me-2"></i>
                        Changer le Mot de Passe
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






