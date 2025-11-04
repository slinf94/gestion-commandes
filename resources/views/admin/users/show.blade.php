@extends('admin.layouts.app')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails de l'Utilisateur: {{ $user->full_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informations Personnelles</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nom:</strong></td>
                                            <td>{{ $user->nom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prénom:</strong></td>
                                            <td>{{ $user->prenom }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Téléphone:</strong></td>
                                            <td>{{ $user->numero_telephone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>WhatsApp:</strong></td>
                                            <td>{{ $user->numero_whatsapp ?? 'Non renseigné' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de naissance:</strong></td>
                                            <td>{{ $user->date_naissance ? $user->date_naissance->format('d/m/Y') : 'Non renseignée' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Informations de Compte</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Rôle:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                @php
                                                    $status = is_object($user->status) ? $user->status->value : $user->status;
                                                    $statusTranslations = [
                                                        'active' => 'Actif',
                                                        'inactive' => 'Inactif',
                                                        'pending' => 'En attente',
                                                        'suspended' => 'Suspendu'
                                                    ];
                                                    $statusColors = [
                                                        'active' => 'success',
                                                        'inactive' => 'secondary',
                                                        'pending' => 'warning',
                                                        'suspended' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge badge-{{ $statusColors[$status] ?? 'secondary' }}">
                                                    {{ $statusTranslations[$status] ?? ucfirst($status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email vérifié:</strong></td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-success">Oui</span>
                                                @else
                                                    <span class="badge badge-warning">Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>2FA activé:</strong></td>
                                            <td>
                                                @if($user->two_factor_enabled)
                                                    <span class="badge badge-success">Oui</span>
                                                @else
                                                    <span class="badge badge-secondary">Non</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Membre depuis:</strong></td>
                                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dernière connexion:</strong></td>
                                            <td>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($user->localisation || $user->quartier)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Adresse</h5>
                                    <div class="border p-3 rounded">
                                        <p class="mb-1">
                                            @if($user->localisation)
                                                <strong>Localisation:</strong> {{ $user->localisation }}<br>
                                            @endif
                                            @if($user->quartier)
                                                <strong>Quartier:</strong> {{ $user->quartier }}<br>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <h5>Photo de Profil</h5>
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" class="img-thumbnail" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-user fa-3x mb-2"></i>
                                    <p>Aucune photo</p>
                                </div>
                            @endif

                            <h5 class="mt-4">Statistiques</h5>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h4 class="text-primary">{{ $user->orders ? $user->orders->count() : 0 }}</h4>
                                        <small>Commandes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h4 class="text-success">{{ $user->cartItems ? $user->cartItems->count() : 0 }}</h4>
                                        <small>Panier</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($user->orders && $user->orders->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Dernières Commandes</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->orders->take(5) as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $order->getStatusClass() }}">
                                                    {{ $order->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
