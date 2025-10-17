@extends('admin.layouts.app')

@section('title', 'Mon Profil')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mon Profil</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user me-2"></i>
                        Informations du Profil
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nom complet</span>
                                    <span class="info-box-number">{{ $user->full_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Email</span>
                                    <span class="info-box-number">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Téléphone</span>
                                    <span class="info-box-number">{{ $user->numero_telephone ?? 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">WhatsApp</span>
                                    <span class="info-box-number">{{ $user->numero_whatsapp ?? 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-secondary">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Localisation</span>
                                    <span class="info-box-number">{{ $user->localisation ?? 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-dark">
                                    <i class="fas fa-home"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Quartier</span>
                                    <span class="info-box-number">{{ $user->quartier ?? 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Rôle</span>
                                    <span class="info-box-number">
                                        <span class="badge badge-success">{{ ucfirst($user->role) }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Membre depuis</span>
                                    <span class="info-box-number">{{ $user->created_at->format('d/m/Y') }}</span>
                                </div>
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
                        <i class="fas fa-cog me-2"></i>
                        Actions Rapides
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Modifier le Profil
                        </a>

                        <a href="{{ route('admin.profile.password') }}" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>
                            Changer le Mot de Passe
                        </a>

                        <a href="{{ route('admin.settings.index') }}" class="btn btn-info">
                            <i class="fas fa-cog me-2"></i>
                            Paramètres
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Statistiques
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Dernière connexion</span>
                            <span class="info-box-number">
                                {{ $user->updated_at->format('d/m/Y à H:i') }}
                            </span>
                        </div>
                    </div>

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Statut</span>
                            <span class="info-box-number">
                                <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection












