@extends('admin.layouts.app')

@section('title', 'Détails de l\'Activité - Allo Mobile Admin')
@section('page-title', 'Détails de l\'Activité')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Détails de l'Activité #{{ $activityLog->id }}</h4>
        <small class="text-muted">Informations complètes sur cette action</small>
    </div>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informations générales -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID :</strong></td>
                                <td>{{ $activityLog->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Description :</strong></td>
                                <td>{{ $activityLog->description }}</td>
                            </tr>
                            <tr>
                                <td><strong>Type de Log :</strong></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $activityLog->log_name ?? 'default' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Date :</strong></td>
                                <td>
                                    {{ $activityLog->created_at->format('d/m/Y H:i:s') }}
                                    <br>
                                    <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Type d'Action :</strong></td>
                                <td>
                                    <span class="activity-badge activity-{{ $activityLog->activity_type }}">
                                        @switch($activityLog->activity_type)
                                            @case('created')
                                                <i class="fas fa-plus-circle me-1"></i>Créé
                                                @break
                                            @case('updated')
                                                <i class="fas fa-edit me-1"></i>Modifié
                                                @break
                                            @case('deleted')
                                                <i class="fas fa-trash me-1"></i>Supprimé
                                                @break
                                            @case('restored')
                                                <i class="fas fa-undo me-1"></i>Restauré
                                                @break
                                            @case('logged_in')
                                                <i class="fas fa-sign-in-alt me-1"></i>Connexion
                                                @break
                                            @case('logged_out')
                                                <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                                                @break
                                            @default
                                                <i class="fas fa-info-circle me-1"></i>Autre
                                        @endswitch
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Modèle :</strong></td>
                                <td>
                                    @if($activityLog->subject_type)
                                        <span class="badge bg-light text-dark">
                                            {{ class_basename($activityLog->subject_type) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Objet ID :</strong></td>
                                <td>{{ $activityLog->subject_id ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détails des modifications -->
        @if($activityLog->properties && !empty($activityLog->properties))
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Détails des Modifications</h5>
            </div>
            <div class="card-body">
                @php
                    $properties = $activityLog->formatted_properties;
                @endphp

                @if(isset($properties['attributes']) || isset($properties['old']))
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Attribut</th>
                                    <th>Ancienne Valeur</th>
                                    <th>Nouvelle Valeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $oldValues = $properties['old'] ?? [];
                                    $newValues = $properties['attributes'] ?? [];
                                    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                                @endphp

                                @foreach($allKeys as $key)
                                <tr>
                                    <td>
                                        <strong>{{ $key }}</strong>
                                    </td>
                                    <td>
                                        @if(isset($oldValues[$key]))
                                            <code class="text-danger">{{ $oldValues[$key] }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($newValues[$key]))
                                            <code class="text-success">{{ $newValues[$key] }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Aucune modification détaillée disponible pour cette activité.
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Propriétés brutes -->
        @if($activityLog->properties)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-code me-2"></i>Propriétés Brutes</h5>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded"><code>{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Informations utilisateur -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Utilisateur</h5>
            </div>
            <div class="card-body">
                @if($activityLog->causer)
                    <div class="text-center">
                        <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ $activityLog->causer->initials }}
                        </div>
                        <h5>{{ $activityLog->causer->full_name }}</h5>
                        <p class="text-muted">{{ $activityLog->causer->email }}</p>
                        <span class="badge bg-{{ $activityLog->causer->status == 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst(is_object($activityLog->causer->status) ? $activityLog->causer->status->value : $activityLog->causer->status) }}
                        </span>
                        <br>
                        <small class="text-muted">
                            Rôle: {{ ucfirst($activityLog->causer->role) }}
                        </small>
                    </div>
                @else
                    <div class="text-center">
                        <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h5>Système</h5>
                        <p class="text-muted">Action automatique</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informations sur l'objet -->
        @if($activityLog->subject)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cube me-2"></i>Objet Concerné</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Type :</strong></td>
                        <td>{{ class_basename($activityLog->subject) }}</td>
                    </tr>
                    <tr>
                        <td><strong>ID :</strong></td>
                        <td>{{ $activityLog->subject->id }}</td>
                    </tr>
                    @if(method_exists($activityLog->subject, 'getRouteKeyName'))
                    <tr>
                        <td><strong>Clé :</strong></td>
                        <td>{{ $activityLog->subject->getRouteKey() }}</td>
                    </tr>
                    @endif
                </table>

                @if(method_exists($activityLog->subject, 'name'))
                    <p><strong>Nom :</strong> {{ $activityLog->subject->name }}</p>
                @endif

                @if(method_exists($activityLog->subject, 'title'))
                    <p><strong>Titre :</strong> {{ $activityLog->subject->title }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>Voir toutes les activités
                    </a>

                    @if($activityLog->causer)
                    <a href="{{ route('admin.activity-logs.index', ['user_id' => $activityLog->causer_id]) }}" class="btn btn-outline-info">
                        <i class="fas fa-user me-2"></i>Activités de cet utilisateur
                    </a>
                    @endif

                    @if($activityLog->subject_type)
                    <a href="{{ route('admin.activity-logs.index', ['subject_type' => $activityLog->subject_type]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cube me-2"></i>Activités de ce modèle
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .activity-badge {
        font-size: 0.8em;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    .activity-created { background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; }
    .activity-updated { background: linear-gradient(135deg, #2196F3, #1976D2); color: white; }
    .activity-deleted { background: linear-gradient(135deg, #f44336, #d32f2f); color: white; }
    .activity-restored { background: linear-gradient(135deg, #FF9800, #F57C00); color: white; }
    .activity-logged_in { background: linear-gradient(135deg, #9C27B0, #7B1FA2); color: white; }
    .activity-logged_out { background: linear-gradient(135deg, #607D8B, #455A64); color: white; }
    .activity-other { background: linear-gradient(135deg, #6C757D, #495057); color: white; }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9em;
    }

    pre {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection


