@extends('admin.layouts.app')

@section('title', 'Paramètres Généraux')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Paramètres</a></li>
    <li class="breadcrumb-item active">Généraux</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs me-2"></i>
                        Paramètres Généraux
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.general.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="app_name">Nom de l'Application *</label>
                            <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                   id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                            @error('app_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Nom affiché dans l'interface utilisateur</small>
                        </div>

                        <div class="form-group">
                            <label for="app_url">URL de l'Application *</label>
                            <input type="url" class="form-control @error('app_url') is-invalid @enderror"
                                   id="app_url" name="app_url" value="{{ old('app_url', $settings['app_url']) }}" required>
                            @error('app_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">URL de base de l'application</small>
                        </div>

                        <div class="form-group">
                            <label for="app_env">Environnement</label>
                            <select class="form-control" id="app_env" name="app_env" disabled>
                                <option value="{{ $settings['app_env'] }}" selected>{{ ucfirst($settings['app_env']) }}</option>
                            </select>
                            <small class="form-text text-muted">L'environnement ne peut pas être modifié ici</small>
                        </div>

                        <div class="form-group">
                            <label for="app_debug">Mode Debug</label>
                            <select class="form-control" id="app_debug" name="app_debug" disabled>
                                <option value="{{ $settings['app_debug'] ? 'true' : 'false' }}" selected>
                                    {{ $settings['app_debug'] ? 'Activé' : 'Désactivé' }}
                                </option>
                            </select>
                            <small class="form-text text-muted">Le mode debug ne peut pas être modifié ici</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Sauvegarder
                            </button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
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
                        <h6><strong>Configuration actuelle :</strong></h6>
                        <ul class="mb-0">
                            <li><strong>Nom :</strong> {{ $settings['app_name'] }}</li>
                            <li><strong>URL :</strong> {{ $settings['app_url'] }}</li>
                            <li><strong>Environnement :</strong> {{ ucfirst($settings['app_env']) }}</li>
                            <li><strong>Debug :</strong> {{ $settings['app_debug'] ? 'Activé' : 'Désactivé' }}</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note :</strong> Certains paramètres nécessitent un redémarrage du serveur pour être pris en compte.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog me-2"></i>
                        Autres Paramètres
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings.security') }}" class="btn btn-warning">
                            <i class="fas fa-shield-alt me-2"></i>
                            Sécurité
                        </a>

                        <a href="{{ route('admin.settings.notifications') }}" class="btn btn-info">
                            <i class="fas fa-bell me-2"></i>
                            Notifications
                        </a>

                        <a href="{{ route('admin.settings.system') }}" class="btn btn-success">
                            <i class="fas fa-server me-2"></i>
                            Système
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






