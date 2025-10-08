@extends('admin.layouts.app')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier l'Utilisateur: {{ $user->fullName }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
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
                                            <label for="numero_telephone">Téléphone *</label>
                                            <input type="text" class="form-control @error('numero_telephone') is-invalid @enderror"
                                                   id="numero_telephone" name="numero_telephone" value="{{ old('numero_telephone', $user->numero_telephone) }}" required>
                                            @error('numero_telephone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="numero_whatsapp">WhatsApp</label>
                                            <input type="text" class="form-control @error('numero_whatsapp') is-invalid @enderror"
                                                   id="numero_whatsapp" name="numero_whatsapp" value="{{ old('numero_whatsapp', $user->numero_whatsapp) }}">
                                            @error('numero_whatsapp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_naissance">Date de naissance</label>
                                            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror"
                                                   id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $user->date_naissance ? $user->date_naissance->format('Y-m-d') : '') }}">
                                            @error('date_naissance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="localisation">Localisation</label>
                                    <input type="text" class="form-control @error('localisation') is-invalid @enderror"
                                           id="localisation" name="localisation" value="{{ old('localisation', $user->localisation) }}">
                                    @error('localisation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="quartier">Quartier</label>
                                    <input type="text" class="form-control @error('quartier') is-invalid @enderror"
                                           id="quartier" name="quartier" value="{{ old('quartier', $user->quartier) }}">
                                    @error('quartier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="role">Rôle</label>
                                    <select class="form-control @error('role') is-invalid @enderror"
                                            id="role" name="role">
                                        <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>Client</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="email_verified" name="email_verified"
                                               value="1" {{ old('email_verified', $user->email_verified_at) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_verified">
                                            Email vérifié
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="two_factor_enabled" name="two_factor_enabled"
                                               value="1" {{ old('two_factor_enabled', $user->two_factor_enabled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="two_factor_enabled">
                                            Authentification à deux facteurs
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="avatar">Photo de profil</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                           id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($user->avatar)
                                        <div class="mt-2">
                                            <img src="{{ $user->avatar }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






