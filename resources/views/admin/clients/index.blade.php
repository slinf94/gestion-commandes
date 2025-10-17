@extends('admin.layouts.app')

@section('title', 'Gestion des Clients CRM - Allo Mobile Admin')
@section('page-title', 'Gestion des Clients CRM')

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

                    <!-- Section de recherche -->
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.clients.search') }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" name="search"
                                               placeholder="Rechercher par nom, email, téléphone..."
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Effacer
                                    </a>
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
<div class="d-flex justify-content-center mt-4">
    {{ $clients->links() }}
</div>
@endsection
