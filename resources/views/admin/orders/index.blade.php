@extends('admin.layouts.app')

@section('title', 'Gestion des Commandes - Allo Mobile Admin')
@section('page-title', 'Gestion des Commandes')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Liste des Commandes</h4>
                    <small class="text-muted">Suivez et gérez toutes les commandes</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="toggleFilters()">
                        <i class="fas fa-filter me-2"></i>Filtres
                    </button>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if(isset($stats))
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $stats['total'] }}</h5>
                            <small>Total Commandes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $stats['pending'] ?? 0 }}</h5>
                            <small>En Attente</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $stats['delivered'] ?? 0 }}</h5>
                            <small>Livrées</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-0">{{ $stats['cancelled'] ?? 0 }}</h5>
                            <small>Annulées</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Filtres (masqués par défaut) -->
    <div class="row mb-4" id="filters-section" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="ID, n° commande, client..." value="{{ request('search') }}">
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
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">
                                        <i class="fas fa-hashtag me-1"></i>ID
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
                                    <th width="15%">
                                        <i class="fas fa-tools me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
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
                                            $itemsCount = \DB::table('order_items')->where('order_id', $order->id)->count();
                                        @endphp
                                        @if($itemsCount > 0)
                                            <span class="badge bg-info fs-6">
                                                <i class="fas fa-shopping-cart me-1"></i>
                                                {{ $itemsCount }} article(s)
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
                                        <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td>
                                        <small>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
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
</style>

<script>
function toggleFilters() {
    const filtersSection = document.getElementById('filters-section');
    const button = event.target;

    if (filtersSection.style.display === 'none') {
        filtersSection.style.display = 'block';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Masquer Filtres';
    } else {
        filtersSection.style.display = 'none';
        button.innerHTML = '<i class="fas fa-filter me-2"></i>Filtres';
    }
}

// Filtrage dynamique
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
