@extends('admin.layouts.app')

@section('title', 'Détails du Client - {{ $client->full_name }} | Allo Mobile Admin')
@section('page-title', 'Détails du Client')

@section('styles')
<style>
    .stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
    .stats-card.success { background: linear-gradient(135deg, #4CAF50, #2E7D32); }
    .stats-card.warning { background: linear-gradient(135deg, #FF9800, #F57C00); }
    .stats-card.danger { background: linear-gradient(135deg, #f44336, #d32f2f); }
    .stats-card.info { background: linear-gradient(135deg, #2196F3, #1976D2); }
    .client-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; }
    .avatar { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; }
    .badge-status { font-size: 0.8em; padding: 8px 12px; border-radius: 20px; }
    .filter-section { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Détails du Client</h4>
        <small class="text-muted">Informations complètes et historique des commandes</small>
    </div>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
                    <div class="client-header">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <div class="avatar mx-auto">
                                    {{ $client->initials }}
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-2">{{ $client->full_name }}</h3>
                                <p class="mb-1">
                                    <i class="fas fa-envelope me-2"></i>
                                    {{ $client->email }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-phone me-2"></i>
                                    {{ $client->numero_telephone ?? 'Non renseigné' }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    {{ $client->full_address ?? 'Adresse non renseignée' }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    Membre depuis le {{ $client->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="col-md-2 text-end">
                                <span class="badge badge-status bg-{{ $client->isActive() ? 'success' : 'warning' }}">
                                    {{ $client->isActive() ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                        <p class="mb-0">Total Commandes</p>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['livrees'] }}</h4>
                                        <p class="mb-0">Livrées</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['encours'] }}</h4>
                                        <p class="mb-0">En Cours</p>
                                    </div>
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card danger">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ $stats['annulees'] }}</h4>
                                        <p class="mb-0">Annulées</p>
                                    </div>
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques financières -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="stats-card info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ number_format($stats['montant_total'], 0, ',', ' ') }} FCFA</h4>
                                        <p class="mb-0">Montant Total</p>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">{{ number_format($stats['montant_moyen'], 0, ',', ' ') }} FCFA</h4>
                                        <p class="mb-0">Panier Moyen</p>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="filter-section">
                        <h5 class="mb-3">Filtrer les commandes</h5>
                        <form id="filterForm" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>En cours</option>
                                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Expédié</option>
                                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date_from" class="form-label">Date de début</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_to" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filtrer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Historique des commandes -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Historique des Commandes
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID Commande</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Montant</th>
                                            <th>Articles</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->order_number }}</strong>
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'pending' => ['text' => 'En attente', 'class' => 'warning'],
                                                        'confirmed' => ['text' => 'Confirmé', 'class' => 'info'],
                                                        'processing' => ['text' => 'En cours', 'class' => 'info'],
                                                        'shipped' => ['text' => 'Expédié', 'class' => 'primary'],
                                                        'delivered' => ['text' => 'Livré', 'class' => 'success'],
                                                        'cancelled' => ['text' => 'Annulé', 'class' => 'danger'],
                                                    ];
                                                    $status = $statusMap[$order->status] ?? ['text' => ucfirst($order->status), 'class' => 'secondary'];
                                                @endphp
                                                <span class="badge bg-{{ $status['class'] }}">
                                                    {{ $status['text'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $order->items->count() }} article(s)
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Aucune commande trouvée</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                {{ $orders->appends(request()->query())->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Auto-submit du formulaire de filtre
    document.getElementById('filterForm').addEventListener('change', function() {
        this.submit();
    });
</script>
@endsection
