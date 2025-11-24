@extends('admin.layouts.app')

@section('title', 'Gestion des Commandes - Allo Mobile Admin')
@section('page-title', 'Gestion des Commandes')

@section('content')
<!-- Header moderne avec gradient vert -->
<div class="card shadow-lg border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header text-white" style="background: linear-gradient(135deg, #38B04A, #4CAF50); padding: 20px;">
        <h3 class="mb-1" style="font-weight: 600; font-size: 1.5rem;">
            <i class="fas fa-shopping-bag me-2"></i>Gestion des Commandes
        </h3>
        <small class="opacity-75">Liste des Commandes - Suivez et gérez toutes les commandes</small>
    </div>
</div>

<div class="container-fluid">
    <!-- Onglets pour séparer Téléphones et Accessoires -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs" id="orderTypeTabs" role="tablist" style="border-bottom: 2px solid #38B04A;">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($productType ?? 'all') === 'all' ? 'active' : '' }}" 
                       href="{{ route('admin.orders.index', array_merge(request()->query(), ['product_type' => 'all'])) }}"
                       style="color: {{ ($productType ?? 'all') === 'all' ? '#38B04A' : '#6c757d' }}; font-weight: {{ ($productType ?? 'all') === 'all' ? '600' : '400' }};">
                        <i class="fas fa-list me-2"></i>Toutes les Commandes
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($productType ?? 'all') === 'telephone' ? 'active' : '' }}" 
                       href="{{ route('admin.orders.index', array_merge(request()->query(), ['product_type' => 'telephone'])) }}"
                       style="color: {{ ($productType ?? 'all') === 'telephone' ? '#38B04A' : '#6c757d' }}; font-weight: {{ ($productType ?? 'all') === 'telephone' ? '600' : '400' }};">
                        <i class="fas fa-mobile-alt me-2"></i>Téléphones
                        @if(isset($statsByType['telephones']['total']) && $statsByType['telephones']['total'] > 0)
                            <span class="badge bg-warning ms-2">{{ $statsByType['telephones']['total'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($productType ?? 'all') === 'accessoire' ? 'active' : '' }}" 
                       href="{{ route('admin.orders.index', array_merge(request()->query(), ['product_type' => 'accessoire'])) }}"
                       style="color: {{ ($productType ?? 'all') === 'accessoire' ? '#38B04A' : '#6c757d' }}; font-weight: {{ ($productType ?? 'all') === 'accessoire' ? '600' : '400' }};">
                        <i class="fas fa-headphones me-2"></i>Accessoires
                        @if(isset($statsByType['accessoires']['total']) && $statsByType['accessoires']['total'] > 0)
                            <span class="badge bg-info ms-2">{{ $statsByType['accessoires']['total'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Statistiques modernes -->
    @if(isset($stats))
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #0066cc;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Total Commandes</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #0066cc;">{{ $stats['total'] }}</h3>
                            @if(isset($statsByType))
                                <small class="text-muted">
                                    📱 {{ $statsByType['telephones']['total'] ?? 0 }} Tél. | 
                                    🔌 {{ $statsByType['accessoires']['total'] ?? 0 }} Acc.
                                </small>
                            @endif
                        </div>
                        <i class="fas fa-chart-line fa-2x" style="color: #0066cc; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">En Attente</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #ffc107;">{{ $stats['pending'] ?? 0 }}</h3>
                            @if(isset($statsByType))
                                <small class="text-muted">
                                    📱 {{ $statsByType['telephones']['pending'] ?? 0 }} Tél. | 
                                    🔌 {{ $statsByType['accessoires']['pending'] ?? 0 }} Acc.
                                </small>
                            @endif
                        </div>
                        <i class="fas fa-clock fa-2x" style="color: #ffc107; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #0dcaf0;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Livrées</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #0dcaf0;">{{ $stats['delivered'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x" style="color: #0dcaf0; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">Annulées</h6>
                            <h3 class="mb-0" style="font-weight: 700; color: #dc3545;">{{ $stats['cancelled'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x" style="color: #dc3545; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtres toujours visibles -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-bottom: 2px solid #38B04A; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0" style="color: #38B04A; font-weight: 600;">
                        <i class="fas fa-filter me-2"></i>Filtres et Recherche
                    </h5>
                </div>
                <div class="card-body" style="padding: 20px;">
                    <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                @include('admin.components.search-input', [
                                    'id' => 'search',
                                    'name' => 'search',
                                    'placeholder' => 'ID, n° commande, client...',
                                    'value' => request('search', ''),
                                    'searchUrl' => route('admin.search.orders'),
                                    'resultKey' => 'data',
                                    'minLength' => 2,
                                    'debounceDelay' => 500
                                ])
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expédié</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="product_type" class="form-label">Type de Produit</label>
                                <select name="product_type" id="product_type" class="form-select">
                                    <option value="all" {{ ($productType ?? 'all') === 'all' ? 'selected' : '' }}>Tous</option>
                                    <option value="telephone" {{ ($productType ?? 'all') === 'telephone' ? 'selected' : '' }}>Téléphones</option>
                                    <option value="accessoire" {{ ($productType ?? 'all') === 'accessoire' ? 'selected' : '' }}>Accessoires</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Date début</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Date fin</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1">
                                <label for="per_page" class="form-label">Par page</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success me-2" style="background: linear-gradient(135deg, #38B04A, #4CAF50); border: none; border-radius: 8px;">
                                    <i class="fas fa-search me-2"></i>Filtrer
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-dismiss" data-dismiss-time="5000" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tableau principal -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">
                                        <i class="fas fa-id-card me-1"></i>ID
                                    </th>
                                    <th width="20%">
                                        <i class="fas fa-user me-1"></i>Client
                                    </th>
                                    <th width="12%">
                                        <i class="fas fa-shopping-cart me-1"></i>Articles
                                    </th>
                                    <th width="15%">
                                        <i class="fas fa-tags me-1"></i>Statut
                                    </th>
                                    <th width="15%">
                                        <i class="fas fa-money-bill me-1"></i>Total
                                    </th>
                                    <th width="15%">
                                        <i class="fas fa-calendar me-1"></i>Date
                                    </th>
                                    <th width="18%">
                                        <i class="fas fa-bolt me-1"></i>Actions Statut
                                    </th>
                                    <th width="12%">
                                        <i class="fas fa-tools me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->id }}</strong>
                                    </td>
                                    <td>
                                        @if($order->user)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <div>
                                                    <strong>{{ $order->user->full_name }}</strong>
                                                    @if($order->user->email)
                                                        <br><small class="text-muted">{{ $order->user->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                Utilisateur supprimé
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Compter les articles selon le filtre actif
                                            $productType = $productType ?? 'all';
                                            
                                            if ($productType === 'telephone') {
                                                // Compter uniquement les téléphones
                                                $itemsCount = $order->items->filter(function($item) {
                                                    return $item->isPhone();
                                                })->sum('quantity');
                                                
                                                $badgeColor = 'bg-primary';
                                                $badgeIcon = 'fa-mobile-alt';
                                                $badgeText = $itemsCount > 0 ? "$itemsCount téléphone" . ($itemsCount > 1 ? 's' : '') : 'Aucun téléphone';
                                            } elseif ($productType === 'accessoire') {
                                                // Compter uniquement les accessoires
                                                $itemsCount = $order->items->filter(function($item) {
                                                    return $item->isAccessory();
                                                })->sum('quantity');
                                                
                                                $badgeColor = 'bg-info';
                                                $badgeIcon = 'fa-headphones';
                                                $badgeText = $itemsCount > 0 ? "$itemsCount accessoire" . ($itemsCount > 1 ? 's' : '') : 'Aucun accessoire';
                                            } else {
                                                // Compter tous les articles ou afficher un résumé par catégorie
                                                $phoneCount = $order->items->filter(function($item) {
                                                    return $item->isPhone();
                                                })->sum('quantity');
                                                
                                                $accessoryCount = $order->items->filter(function($item) {
                                                    return $item->isAccessory();
                                                })->sum('quantity');
                                                
                                                $otherCount = $order->items->filter(function($item) {
                                                    return !$item->isPhone() && !$item->isAccessory();
                                                })->sum('quantity');
                                                
                                                $totalCount = $phoneCount + $accessoryCount + $otherCount;
                                                $itemsCount = $totalCount;
                                                
                                                // Afficher un résumé si commande mixte
                                                if ($phoneCount > 0 && $accessoryCount > 0) {
                                                    $badgeColor = 'bg-info';
                                                    $badgeIcon = 'fa-shopping-cart';
                                                    $parts = [];
                                                    if ($phoneCount > 0) {
                                                        $parts[] = "$phoneCount téléphone" . ($phoneCount > 1 ? 's' : '');
                                                    }
                                                    if ($accessoryCount > 0) {
                                                        $parts[] = "$accessoryCount accessoire" . ($accessoryCount > 1 ? 's' : '');
                                                    }
                                                    if ($otherCount > 0) {
                                                        $parts[] = "$otherCount autre" . ($otherCount > 1 ? 's' : '');
                                                    }
                                                    $badgeText = implode(', ', $parts);
                                                } elseif ($phoneCount > 0) {
                                                    $badgeColor = 'bg-primary';
                                                    $badgeIcon = 'fa-mobile-alt';
                                                    $badgeText = "$phoneCount téléphone" . ($phoneCount > 1 ? 's' : '');
                                                } elseif ($accessoryCount > 0) {
                                                    $badgeColor = 'bg-info';
                                                    $badgeIcon = 'fa-headphones';
                                                    $badgeText = "$accessoryCount accessoire" . ($accessoryCount > 1 ? 's' : '');
                                                } else {
                                                    $badgeColor = 'bg-info';
                                                    $badgeIcon = 'fa-shopping-cart';
                                                    $badgeText = "$totalCount article" . ($totalCount > 1 ? 's' : '');
                                                }
                                            }
                                        @endphp
                                        @if($itemsCount > 0)
                                            <span class="badge {{ $badgeColor ?? 'bg-info' }} fs-6">
                                                <i class="fas {{ $badgeIcon ?? 'fa-shopping-cart' }} me-1"></i>
                                                {{ $badgeText ?? "$itemsCount article(s)" }}
                                            </span>
                                        @else
                                            <span class="text-muted">Aucun article</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusClass() }} fs-6">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $canViewRevenue = \App\Helpers\AdminMenuHelper::canSee(auth()->user(), 'super-admin', 'admin');
                                        @endphp
                                        @if($canViewRevenue)
                                            <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
                                        @else
                                            <span class="text-muted">***</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $status = is_string($order->status) ? $order->status : ($order->status->value ?? 'pending');
                                            $isPending = $status === 'pending';
                                            $isConfirmed = $status === 'confirmed';
                                            $isProcessing = $status === 'processing';
                                            $isShipped = $status === 'shipped';
                                            $isDelivered = $status === 'delivered';
                                            $isTerminal = in_array($status, ['cancelled','delivered','completed']);
                                        @endphp
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($isPending)
                                                <button type="button"
                                                        class="btn btn-sm btn-success order-quick-action"
                                                        data-order-id="{{ $order->id }}"
                                                        data-new-status="confirmed"
                                                        title="Valider la commande">
                                                    <i class="fas fa-check me-1"></i>Valider
                                                </button>
                                            @endif
                                            @if($isConfirmed)
                                                <button type="button"
                                                        class="btn btn-sm btn-primary order-quick-action"
                                                        data-order-id="{{ $order->id }}"
                                                        data-new-status="processing"
                                                        title="Mettre en traitement">
                                                    <i class="fas fa-cog me-1"></i>En traitement
                                                </button>
                                            @endif
                                            @if($isProcessing)
                                                <button type="button"
                                                        class="btn btn-sm btn-info order-quick-action"
                                                        data-order-id="{{ $order->id }}"
                                                        data-new-status="shipped"
                                                        title="Expédier la commande">
                                                    <i class="fas fa-shipping-fast me-1"></i>Expédier
                                                </button>
                                            @endif
                                            @if($isShipped)
                                                <button type="button"
                                                        class="btn btn-sm btn-success order-quick-action"
                                                        data-order-id="{{ $order->id }}"
                                                        data-new-status="delivered"
                                                        title="Marquer comme livrée">
                                                    <i class="fas fa-check-double me-1"></i>Livrée
                                                </button>
                                            @endif
                                            @if(!$isTerminal)
                                                <button type="button"
                                                        class="btn btn-sm btn-danger order-quick-cancel"
                                                        data-order-id="{{ $order->id }}"
                                                        data-new-status="cancelled"
                                                        title="Annuler la commande">
                                                    <i class="fas fa-times me-1"></i>Annuler
                                                </button>
                                            @endif
                                            @if($isTerminal)
                                                <span class="badge bg-secondary">Aucune action</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @php
                                                $status = is_string($order->status) ? $order->status : ($order->status->value ?? 'pending');
                                                $isCancelled = $status === 'cancelled';
                                            @endphp
                                            @if($isCancelled)
                                                {{-- Bouton Supprimer (seulement si annulée) --}}
                                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                                      id="delete-order-{{ $order->id }}"
                                                      class="d-inline delete-order-form">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-order-btn"
                                                        data-form-id="delete-order-{{ $order->id }}"
                                                        data-order-id="{{ $order->id }}"
                                                        title="Supprimer définitivement">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                {{-- Bouton Annuler (si pas annulée) --}}
                                                <button type="button" class="btn btn-sm btn-outline-warning cancel-order-btn"
                                                        data-order-id="{{ $order->id }}"
                                                        title="Annuler la commande">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune commande trouvée</h5>
                                            <p class="text-muted">Aucune commande ne correspond à vos critères de recherche.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($orders) && $orders->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $orders->firstItem() }} à {{ $orders->lastItem() }} sur {{ $orders->total() }} résultats
                        </div>
                        <div>
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-2 > * + * {
    margin-left: 0.5rem;
}

.empty-state {
    padding: 2rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info, .card.bg-danger {
    border: none;
}

.table-light th {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

#filters-section .card {
    border: 1px solid #dee2e6;
}

.auto-dismiss {
    border-radius: 8px;
}
</style>

<script>
// Auto-dismiss alertes
document.addEventListener('DOMContentLoaded', function() {
    // Fermer automatiquement les alertes avec la classe auto-dismiss
    const autoDismissAlerts = document.querySelectorAll('.auto-dismiss');
    autoDismissAlerts.forEach(alert => {
        const dismissTime = alert.getAttribute('data-dismiss-time') || 5000;
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, parseInt(dismissTime));
    });

    // Filtrage dynamique
    const form = document.getElementById('filterForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select');

        inputs.forEach(input => {
            input.addEventListener('change', function() {
                form.submit();
            });

            if (input.type === 'text') {
                input.addEventListener('keyup', function() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        form.submit();
                    }, 500);
                });
            }
        });
    }

    // Gérer les boutons de suppression de commandes (seulement pour les commandes annulées)
    const deleteOrderButtons = document.querySelectorAll('.delete-order-btn');

    deleteOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-form-id');
            const orderId = this.getAttribute('data-order-id');
            const form = document.getElementById(formId);

            if (!form) return;

            customConfirm(
                `Êtes-vous sûr de vouloir supprimer définitivement la commande ${orderId} ? Cette action est irréversible et supprimera toutes les données de la commande.`,
                function() {
                    form.submit();
                },
                null,
                'Suppression définitive',
                'Oui, supprimer',
                'Annuler'
            );
        });
    });

    // Gérer les boutons d'annulation depuis la colonne Actions
    const cancelOrderButtons = document.querySelectorAll('.cancel-order-btn');

    cancelOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const that = this;

            customCancelOrder(
                orderId,
                function onConfirm(reason) {
                    that.disabled = true;
                    postStatus(orderId, 'cancelled', reason || '')
                        .then(() => {
                            showAlert('Commande annulée avec succès', 'success', 'Succès');
                            setTimeout(() => window.location.reload(), 600);
                        })
                        .catch(err => {
                            showAlert(err.message || 'Erreur lors de l\'annulation', 'error', 'Erreur');
                        })
                        .finally(() => { that.disabled = false; });
                },
                null,
                'Confirmation d\'annulation',
                'Oui, annuler',
                'Non'
            );
        });
    });

    // Actions rapides statut
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function postStatus(orderId, newStatus, comment = '') {
        const url = `{{ url('admin/orders') }}/${orderId}/status`;
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus, comment: comment || '' })
        }).then(async (res) => {
            const isJson = (res.headers.get('content-type') || '').includes('application/json');
            const data = isJson ? await res.json() : {};
            if (!res.ok || (data && data.success === false)) {
                const msg = (data && (data.message || data.error)) || `Erreur ${res.status}`;
                throw new Error(msg);
            }
            return data;
        });
    }

    document.querySelectorAll('.order-quick-action').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const newStatus = this.getAttribute('data-new-status');
            this.disabled = true;
            postStatus(orderId, newStatus)
                .then(() => {
                    showAlert('Statut mis à jour avec succès', 'success', 'Succès');
                    setTimeout(() => window.location.reload(), 600);
                })
                .catch(err => {
                    showAlert(err.message || 'Erreur lors de la mise à jour', 'error', 'Erreur');
                })
                .finally(() => { this.disabled = false; });
        });
    });

    document.querySelectorAll('.order-quick-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const newStatus = this.getAttribute('data-new-status');
            const that = this;
            customCancelOrder(
                orderId,
                function onConfirm(reason) {
                    that.disabled = true;
                    postStatus(orderId, newStatus, reason || '')
                        .then(() => {
                            showAlert('Commande annulée avec succès', 'success', 'Succès');
                            setTimeout(() => window.location.reload(), 600);
                        })
                        .catch(err => {
                            showAlert(err.message || 'Erreur lors de l\'annulation', 'error', 'Erreur');
                        })
                        .finally(() => { that.disabled = false; });
                },
                null,
                'Confirmation d\'annulation',
                'Oui, annuler',
                'Non'
            );
        });
    });
});
</script>
@endsection
