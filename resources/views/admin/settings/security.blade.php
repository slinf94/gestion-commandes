@extends('admin.layouts.app')

@section('title', 'Paramètres de Sécurité')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Paramètres</a></li>
    <li class="breadcrumb-item active">Sécurité</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Paramètres de Sécurité
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0" style="color: #38B04A; font-weight: 600;">
                                <i class="fas fa-lock me-2"></i>
                                Authentification
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="fw-bold">Mot de passe actuel</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" value="••••••••" disabled>
                                    <div class="input-group-append">
                                        <a href="{{ route('admin.profile.password') }}" class="btn btn-success" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Fonctionnalité disponible : Vous pouvez modifier votre mot de passe à tout moment.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-key me-2"></i>
                                Sessions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Session active :</strong> Vous êtes connecté depuis {{ auth()->user()->updated_at->format('d/m/Y à H:i') }}
                            </div>

                            <div class="form-group">
                                <label class="fw-bold">Durée de session</label>
                                <select class="form-control" disabled style="background-color: #f8f9fa;">
                                    <option>2 heures (par défaut)</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Fonctionnalité à venir : Vous pourrez configurer la durée d'inactivité avant déconnexion automatique.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0" style="color: #38B04A; font-weight: 600;">
                                <i class="fas fa-ban me-2"></i>
                                Restrictions d'Accès
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="fw-bold">Adresses IP autorisées</label>
                                <input type="text" class="form-control" value="Toutes les adresses IP" disabled style="background-color: #f8f9fa;">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Fonctionnalité à venir : Vous pourrez restreindre l'accès à des adresses IP spécifiques pour renforcer la sécurité.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Sécurité Actuelle
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h6><strong>Mesures actives :</strong></h6>
                        <ul class="mb-0">
                            <li>✓ Authentification obligatoire</li>
                            <li>✓ Sessions sécurisées</li>
                            <li>✓ Protection CSRF</li>
                            <li>✓ Validation des données</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog me-2"></i>
                        Actions Rapides
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.profile.password') }}" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>
                            Changer Mot de Passe
                        </a>

                        <a href="{{ route('admin.profile.show') }}" class="btn btn-info">
                            <i class="fas fa-user me-2"></i>
                            Mon Profil
                        </a>

                        <button type="button" class="btn btn-danger"
                                onclick="customConfirm('Êtes-vous sûr de vouloir fermer toutes les sessions ? Cette action déconnectera tous les utilisateurs de leurs comptes.', function() { alert('Fonctionnalité à venir'); }, null, 'Fermeture de session', 'Confirmer', 'Annuler')">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Fermer Toutes les Sessions
                        </button>
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
                        <a href="{{ route('admin.settings.general') }}" class="btn btn-primary">
                            <i class="fas fa-cogs me-2"></i>
                            Généraux
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
























