@extends('admin.layouts.app')

@section('title', 'Paramètres')

@section('breadcrumb')
    <li class="breadcrumb-item active">Paramètres</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog me-2"></i>
                        Paramètres de l'Administration
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Paramètres Généraux -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cogs me-2"></i>
                                        Paramètres Généraux
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Configuration générale de l'application, nom, URL et environnement.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('admin.settings.general') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Accéder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Sécurité -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Sécurité
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Paramètres de sécurité, authentification et permissions.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('admin.settings.security') }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Accéder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-bell me-2"></i>
                                        Notifications
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Configuration des notifications email, SMS et push.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('admin.settings.notifications') }}" class="btn btn-info btn-block">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Accéder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Système -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-server me-2"></i>
                                        Informations Système
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Informations sur le serveur, PHP, Laravel et configuration.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('admin.settings.system') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Accéder
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tools me-2"></i>
                                        Maintenance
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Outils de maintenance : cache, logs et optimisation.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-block"
                                                onclick="return confirm('Êtes-vous sûr de vouloir vider le cache ?')">
                                            <i class="fas fa-trash me-2"></i>
                                            Vider le Cache
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Profil -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Mon Profil
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Gérer votre profil administrateur et vos informations personnelles.
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('admin.profile.show') }}" class="btn btn-dark btn-block">
                                        <i class="fas fa-arrow-right me-2"></i>
                                        Accéder
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






