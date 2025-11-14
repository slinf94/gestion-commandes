@extends('admin.layouts.app')

@section('title', 'Tableau de Bord - Allo Mobile Admin')
@section('page-title', 'Tableau de Bord')

@section('content')
@php
    use App\Helpers\AdminMenuHelper;
    $user = auth()->user();
    $canViewRevenue = AdminMenuHelper::canSee($user, 'super-admin', 'admin');
    $canViewUsers = AdminMenuHelper::canSee($user, 'super-admin', 'admin');
@endphp

<!-- En-tête avec gradient vert -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%);">
    <div class="card-body text-white p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1" style="font-weight: 600;">
                    <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord
                </h2>
                <small class="opacity-75">Vue d'ensemble de votre activité</small>
            </div>
            <div>
                <i class="fas fa-chart-line fa-3x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques principales - Section 1 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-chart-bar me-2 text-success"></i>Statistiques Générales
        </h5>
    </div>
    
    @if($canViewUsers)
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px; border-left: 4px solid #38B04A;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 text-success fw-bold">{{ $stats['total_users'] ?? 0 }}</h3>
                        <small class="text-muted">Utilisateurs</small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px; border-left: 4px solid #38B04A;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 text-success fw-bold">{{ $stats['total_products'] ?? 0 }}</h3>
                        <small class="text-muted">Produits</small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-box fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px; border-left: 4px solid #ffc107;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 text-warning fw-bold">{{ $stats['total_orders'] ?? 0 }}</h3>
                        <small class="text-muted">Commandes</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-shopping-bag fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($canViewRevenue)
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px; border-left: 4px solid #38B04A;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 text-success fw-bold">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</h3>
                        <small class="text-muted">Chiffre d'affaires</small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-chart-line fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Alertes et Actions - Section 2 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Alertes & Actions Rapides
        </h5>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-warning" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-warning mb-0 fw-bold">{{ $stats['draft_products'] ?? 0 }}</h4>
                        <small class="text-muted">Produits en brouillon</small>
                    </div>
                    <i class="fas fa-file-alt fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-danger" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-danger mb-0 fw-bold">{{ $stats['out_of_stock'] ?? 0 }}</h4>
                        <small class="text-muted">Rupture de stock</small>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-warning" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-warning mb-0 fw-bold">{{ $stats['low_stock'] ?? 0 }}</h4>
                        <small class="text-muted">Stock faible</small>
                    </div>
                    <i class="fas fa-battery-quarter fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm" style="border-radius: 10px; border-left: 4px solid #38B04A;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-success mb-0 fw-bold">{{ $stats['pending_orders'] ?? 0 }}</h4>
                        <small class="text-muted">Commandes en attente</small>
                    </div>
                    <i class="fas fa-clock fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions Rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-light" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0 text-success">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success w-100" style="border-radius: 8px;">
                            <i class="fas fa-plus me-2"></i>Nouveau Produit
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-success w-100" style="border-radius: 8px;">
                            <i class="fas fa-users me-2"></i>Gérer Utilisateurs
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-warning w-100" style="border-radius: 8px;">
                            <i class="fas fa-shopping-bag me-2"></i>Voir Commandes
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-success w-100" style="border-radius: 8px;">
                            <i class="fas fa-history me-2"></i>Journal Activités
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Commandes et Produits - Section 3 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-shopping-cart me-2 text-success"></i>Commandes & Produits
        </h5>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Commandes Récentes
                </h5>
            </div>
            <div class="card-body">
                @if(isset($recent_orders) && $recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
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
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->user_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($canViewRevenue)
                                            <span class="text-success fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="text-muted">***</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($order->status) {
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'processing' => 'success',
                                                'shipped' => 'info',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($order->status) {
                                                'pending' => 'En attente',
                                                'confirmed' => 'Confirmée',
                                                'processing' => 'En traitement',
                                                'shipped' => 'Expédiée',
                                                'delivered' => 'Livrée',
                                                'cancelled' => 'Annulée',
                                                default => ucfirst($order->status)
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            try {
                                                $orderDate = is_string($order->created_at) 
                                                    ? \Carbon\Carbon::parse($order->created_at) 
                                                    : ($order->created_at instanceof \Carbon\Carbon 
                                                        ? $order->created_at 
                                                        : \Carbon\Carbon::parse($order->created_at));
                                            } catch (\Exception $e) {
                                                $orderDate = \Carbon\Carbon::now();
                                            }
                                        @endphp
                                        <small>{{ $orderDate->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-success">
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
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Répartition par Statut
                </h5>
            </div>
            <div class="card-body">
                @if(isset($orders_by_status) && $orders_by_status->count() > 0)
                    <div class="list-group list-group-flush">
                        @php
                            $statusLabels = [
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'processing' => 'En traitement',
                                'shipped' => 'Expédiée',
                                'delivered' => 'Livrée',
                                'cancelled' => 'Annulée'
                            ];
                            $statusColors = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'processing' => 'success',
                                'shipped' => 'info',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                        @endphp
                        @foreach($orders_by_status as $status => $data)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} me-2">{{ $data->count ?? 0 }}</span>
                            <span class="flex-grow-1">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                            @if($canViewRevenue)
                                <small class="text-muted">{{ number_format($data->revenue ?? 0, 0, ',', ' ') }} FCFA</small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucune donnée</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Produits les plus vendus - Section 4 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-trophy me-2 text-success"></i>Produits les Plus Vendus
        </h5>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Tous les temps
                </h5>
            </div>
            <div class="card-body">
                @if(isset($top_products) && $top_products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Marque</th>
                                    <th>Vendu</th>
                                    <th>Revenus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($top_products as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td><small class="text-muted">{{ $product->brand ?? 'N/A' }}</small></td>
                                    <td><span class="badge bg-success">{{ $product->total_sold }}</span></td>
                                    <td>
                                        @if($canViewRevenue)
                                            <span class="text-success fw-bold">{{ number_format($product->total_revenue ?? 0, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="text-muted">***</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucun produit vendu</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Ce Mois
                </h5>
            </div>
            <div class="card-body">
                @if(isset($top_products_month) && $top_products_month->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Marque</th>
                                    <th>Vendu</th>
                                    <th>Revenus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($top_products_month as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td><small class="text-muted">{{ $product->brand ?? 'N/A' }}</small></td>
                                    <td><span class="badge bg-success">{{ $product->total_sold }}</span></td>
                                    <td>
                                        @if($canViewRevenue)
                                            <span class="text-success fw-bold">{{ number_format($product->total_revenue ?? 0, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="text-muted">***</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucun produit vendu ce mois</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Produits à compléter et alertes - Section 5 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-exclamation-circle me-2 text-warning"></i>Produits à Compléter & Alertes Stock
        </h5>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-warning" style="border-radius: 10px;">
            <div class="card-header bg-warning text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>Produits en Brouillon
                </h5>
            </div>
            <div class="card-body">
                @if(isset($draft_products) && $draft_products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($draft_products as $product)
                        <a href="{{ route('admin.products.edit', $product->slug ?? $product->id) }}" 
                           class="list-group-item list-group-item-action border-0">
                            <div class="d-flex justify-content-between">
                                <span><strong>{{ $product->name }}</strong></span>
                                <small class="text-muted">
                                    @php
                                        try {
                                            $updatedAt = is_string($product->updated_at) 
                                                ? \Carbon\Carbon::parse($product->updated_at) 
                                                : ($product->updated_at instanceof \Carbon\Carbon 
                                                    ? $product->updated_at 
                                                    : \Carbon\Carbon::parse($product->updated_at));
                                        } catch (\Exception $e) {
                                            $updatedAt = \Carbon\Carbon::now();
                                        }
                                    @endphp
                                    {{ $updatedAt->diffForHumans() }}
                                </small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.products.index', ['status' => 'draft']) }}" class="btn btn-sm btn-warning w-100">
                            Voir tous les brouillons
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucun produit en brouillon</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-danger" style="border-radius: 10px;">
            <div class="card-header bg-danger text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Rupture de Stock
                </h5>
            </div>
            <div class="card-body">
                @if(isset($out_of_stock_products) && $out_of_stock_products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($out_of_stock_products as $product)
                        <a href="{{ route('admin.products.edit', $product->slug ?? $product->id) }}" 
                           class="list-group-item list-group-item-action border-0">
                            <div class="d-flex justify-content-between">
                                <span><strong>{{ $product->name }}</strong></span>
                                <span class="badge bg-danger">0</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.products.index', ['stock_available' => 'no']) }}" class="btn btn-sm btn-danger w-100">
                            Voir toutes les ruptures
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucune rupture de stock</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-warning" style="border-radius: 10px;">
            <div class="card-header bg-warning text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-battery-quarter me-2"></i>Stock Faible
                </h5>
            </div>
            <div class="card-body">
                @if(isset($low_stock_products) && $low_stock_products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($low_stock_products as $product)
                        <a href="{{ route('admin.products.edit', $product->slug ?? $product->id) }}" 
                           class="list-group-item list-group-item-action border-0">
                            <div class="d-flex justify-content-between">
                                <span><strong>{{ $product->name }}</strong></span>
                                <span class="badge bg-warning">{{ $product->stock_quantity }} / {{ $product->min_stock_alert }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucun stock faible</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par catégorie et marque - Section 6 -->
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-chart-bar me-2 text-success"></i>Statistiques Détaillées
        </h5>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Par Catégorie
                </h5>
            </div>
            <div class="card-body">
                @if(isset($category_stats) && $category_stats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Total</th>
                                    <th>Actifs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category_stats as $stat)
                                <tr>
                                    <td><strong>{{ $stat->name }}</strong></td>
                                    <td><span class="badge bg-success">{{ $stat->product_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $stat->active_count }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucune donnée</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-mobile-alt me-2"></i>Top 10 Marques
                </h5>
            </div>
            <div class="card-body">
                @if(isset($brand_stats) && $brand_stats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Marque</th>
                                    <th>Total</th>
                                    <th>Actifs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brand_stats as $stat)
                                <tr>
                                    <td><strong>{{ $stat->brand }}</strong></td>
                                    <td><span class="badge bg-success">{{ $stat->product_count }}</span></td>
                                    <td><span class="badge bg-success">{{ $stat->active_count }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Aucune donnée</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ventes journalières et mensuelles - Section 7 -->
@if($canViewRevenue && (isset($daily_sales) || isset($monthly_sales)))
<div class="row mb-4">
    <div class="col-12 mb-3">
        <h5 class="text-muted mb-3">
            <i class="fas fa-chart-line me-2 text-success"></i>Analyse des Ventes
        </h5>
    </div>
    
    @if(isset($daily_sales) && $daily_sales->count() > 0)
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-day me-2"></i>Ventes Journalières (7 derniers jours)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Commandes</th>
                                <th>Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daily_sales as $sale)
                            <tr>
                                <td>
                                    @php
                                        try {
                                            $saleDate = is_string($sale->date) 
                                                ? \Carbon\Carbon::parse($sale->date) 
                                                : \Carbon\Carbon::parse($sale->date);
                                        } catch (\Exception $e) {
                                            $saleDate = \Carbon\Carbon::now();
                                        }
                                    @endphp
                                    <strong>{{ $saleDate->format('d/m/Y') }}</strong>
                                </td>
                                <td><span class="badge bg-success">{{ $sale->orders }}</span></td>
                                <td><span class="text-success fw-bold">{{ number_format($sale->revenue ?? 0, 0, ',', ' ') }} FCFA</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if(isset($monthly_sales) && $monthly_sales->count() > 0)
    <div class="col-md-6">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-success text-white" style="border-radius: 10px 10px 0 0;">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Ventes Mensuelles (12 derniers mois)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>Commandes</th>
                                <th>Revenus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthly_sales as $sale)
                            <tr>
                                <td>
                                    @php
                                        try {
                                            $saleMonth = is_string($sale->month) 
                                                ? \Carbon\Carbon::createFromFormat('Y-m', $sale->month) 
                                                : \Carbon\Carbon::createFromFormat('Y-m', $sale->month);
                                        } catch (\Exception $e) {
                                            $saleMonth = \Carbon\Carbon::now();
                                        }
                                    @endphp
                                    <strong>{{ $saleMonth->format('M Y') }}</strong>
                                </td>
                                <td><span class="badge bg-success">{{ $sale->orders }}</span></td>
                                <td><span class="text-success fw-bold">{{ number_format($sale->revenue ?? 0, 0, ',', ' ') }} FCFA</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
@endsection
