@extends('admin.layouts.app')

@section('title', 'Gestion des Clients - Allo Mobile Admin')
@section('page-title', 'Gestion des Clients')

@section('styles')
<style>
    .client-card { border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: transform 0.3s; }
    .client-card:hover { transform: translateY(-5px); }
    .avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    .stats-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px; padding: 4px 12px; font-size: 0.8em; }
    .search-section { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
</style>
@endsection

@section('content')

<!-- Statistiques -->
@if(isset($stats))
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['total'] }}</h3>
                <p class="mb-0">Total Clients</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['with_orders'] }}</h3>
                <p class="mb-0">Avec Commandes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['new_this_month'] }}</h3>
                <p class="mb-0">Nouveaux ce Mois</p>
            </div>
        </div>
    </div>
</div>
@endif

                    <!-- Section de recherche -->
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.clients.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Rechercher par nom, email, téléphone..."
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="quartier_id" class="form-control">
                                        <option value="">Tous les quartiers</option>
                                        @foreach($quartiers ?? [] as $quartier)
                                            <option value="{{ $quartier->id }}" {{ request('quartier_id') == $quartier->id ? 'selected' : '' }}>
                                                {{ $quartier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="start_date" class="form-control" placeholder="Date début" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="end_date" class="form-control" placeholder="Date fin" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-1">
                                    <select name="per_page" class="form-control">
                                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Liste des clients -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Adresse</th>
                                    <th>Commandes</th>
                                    <th>Dernière Commande</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                {{ $client->initials }}
                                            </div>
                                            <div>
                                                <strong>{{ $client->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">ID: #{{ $client->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="fas fa-envelope me-1"></i>
                                            {{ $client->email }}
                                        </div>
                                        @if($client->numero_telephone)
                                        <div>
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $client->numero_telephone }}
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $client->full_address ?? 'Non renseignée' }}</small>
                                    </td>
                                    <td>
                                        <span class="stats-badge">
                                            {{ $client->orders_count }} commande(s)
                                        </span>
                                    </td>
                                    <td>
                                        @if($client->orders->count() > 0)
                                            @php $lastOrder = $client->orders->first(); @endphp
                                            <div>
                                                <strong>{{ number_format($lastOrder->total_amount, 0, ',', ' ') }} FCFA</strong>
                                            </div>
                                            <small class="text-muted">
                                                {{ $lastOrder->created_at->format('d/m/Y') }}
                                            </small>
                                        @else
                                            <span class="text-muted">Aucune commande</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $client->isActive() ? 'success' : 'warning' }}">
                                            {{ $client->isActive() ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucun client trouvé</p>
                                        @if(request('search'))
                                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary">
                                                Voir tous les clients
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

<!-- Pagination -->
@if(isset($clients) && $clients->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted">
        Affichage de {{ $clients->firstItem() }} à {{ $clients->lastItem() }} sur {{ $clients->total() }} résultats
    </div>
    <div>
        {{ $clients->appends(request()->query())->links() }}
    </div>
</div>
@endif
@endsection
