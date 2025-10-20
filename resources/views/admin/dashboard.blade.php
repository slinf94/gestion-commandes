@extends('admin.layouts.app')

@section('title', 'Tableau de Bord - Allo Mobile Admin')
@section('page-title', 'Tableau de Bord')

@section('content')
<div class="row">
    <!-- Statistiques principales -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_users'] ?? 0 }}</h4>
                        <p class="card-text">Utilisateurs</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_products'] ?? 0 }}</h4>
                        <p class="card-text">Produits</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_orders'] ?? 0 }}</h4>
                        <p class="card-text">Commandes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-bag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_revenue'] ?? 0 }} FCFA</h4>
                        <p class="card-text">Chiffre d'affaires</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            Nouveau Produit
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-users me-2"></i>
                            Gérer Utilisateurs
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Voir Commandes
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-history me-2"></i>
                            Journal Activités
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commandes récentes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Commandes Récentes
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recent_orders) && $recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->nom ?? 'N/A' }} {{ $order->user->prenom ?? '' }}</td>
                                    <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusClass() }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune commande récente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
