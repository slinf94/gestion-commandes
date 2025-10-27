@extends('admin.layouts.app')

@section('title', 'Informations Système')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Paramètres</a></li>
    <li class="breadcrumb-item active">Système</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-server me-2"></i>
                        Informations Système
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- PHP Version -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fab fa-php"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Version PHP</span>
                                    <span class="info-box-number">{{ $systemInfo['php_version'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Laravel Version -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fab fa-laravel"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Version Laravel</span>
                                    <span class="info-box-number">{{ $systemInfo['laravel_version'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Server Software -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-server"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Serveur</span>
                                    <span class="info-box-number">{{ $systemInfo['server_software'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Memory Limit -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-memory"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Limite Mémoire</span>
                                    <span class="info-box-number">{{ $systemInfo['memory_limit'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Max Execution Time -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Temps d'Exécution Max</span>
                                    <span class="info-box-number">{{ $systemInfo['max_execution_time'] }}s</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Max Filesize -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary">
                                    <i class="fas fa-upload"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Taille Max Upload</span>
                                    <span class="info-box-number">{{ $systemInfo['upload_max_filesize'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Post Max Size -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-dark">
                                    <i class="fas fa-file-upload"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Taille Max POST</span>
                                    <span class="info-box-number">{{ $systemInfo['post_max_size'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions de Maintenance -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-tools me-2"></i>
                                        Actions de Maintenance
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-block"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir vider le cache ?')">
                                                    <i class="fas fa-trash me-2"></i>
                                                    Vider le Cache
                                                </button>
                                            </form>
                                        </div>

                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <button type="button" class="btn btn-info btn-block" onclick="location.reload()">
                                                <i class="fas fa-sync me-2"></i>
                                                Actualiser la Page
                                            </button>
                                        </div>

                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <button type="button" class="btn btn-success btn-block"
                                                    onclick="alert('Fonctionnalité à venir')">
                                                <i class="fas fa-database me-2"></i>
                                                Optimiser DB
                                            </button>
                                        </div>

                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <button type="button" class="btn btn-secondary btn-block"
                                                    onclick="alert('Fonctionnalité à venir')">
                                                <i class="fas fa-file-alt me-2"></i>
                                                Voir les Logs
                                            </button>
                                        </div>
                                    </div>
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























