@extends('admin.layouts.app')

@section('title', 'Paramètres de Notification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Paramètres</a></li>
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell me-2"></i>
                        Paramètres de Notification
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    Notifications Email
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="email_notifications"
                                           name="email_notifications" value="1" checked>
                                    <label class="form-check-label" for="email_notifications">
                                        Activer les notifications email
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Recevoir des notifications par email pour les nouvelles commandes, utilisateurs, etc.
                                </small>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-sms me-2"></i>
                                    Notifications SMS
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="sms_notifications"
                                           name="sms_notifications" value="1">
                                    <label class="form-check-label" for="sms_notifications">
                                        Activer les notifications SMS
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Recevoir des notifications importantes par SMS.
                                </small>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    Notifications Push
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="push_notifications"
                                           name="push_notifications" value="1" checked>
                                    <label class="form-check-label" for="push_notifications">
                                        Activer les notifications push
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Recevoir des notifications push dans le navigateur.
                                </small>
                            </div>
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
                        Types de Notifications
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><strong>Notifications disponibles :</strong></h6>
                        <ul class="mb-0">
                            <li>Nouvelles commandes</li>
                            <li>Nouveaux utilisateurs</li>
                            <li>Alerte stock bas</li>
                            <li>Messages clients</li>
                            <li>Maintenance système</li>
                        </ul>
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

                        <a href="{{ route('admin.settings.security') }}" class="btn btn-warning">
                            <i class="fas fa-shield-alt me-2"></i>
                            Sécurité
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
















