@extends('admin.layouts.app')

@section('title', 'Clients du Quartier - ' . $quartier->nom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Clients du Quartier</h1>
                    <p class="text-muted mb-0">{{ $quartier->nom }}, {{ $quartier->ville }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.quartiers.show', $quartier) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour au quartier
                    </a>
                    <a href="{{ route('admin.quartiers.export-clients', $quartier) }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Exporter
                    </a>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.quartiers.clients', $quartier) }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">Recherche</label>
                                <input type="text" class="form-control" id="search" name="search"
                                       value="{{ request('search') }}" placeholder="Nom, email...">
                            </div>
                            <div class="col-md-2">
                                <label for="status">Statut</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Tous</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from">Date début</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to">Date fin</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="sort_by">Trier par</label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
                                    <option value="nom" {{ request('sort_by') == 'nom' ? 'selected' : '' }}>Nom</option>
                                    <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Statut</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="sort_order">Ordre</label>
                                <select class="form-control" id="sort_order" name="sort_order">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                                <a href="{{ route('admin.quartiers.clients', $quartier) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Effacer
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des clients -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Clients ({{ $clients->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($clients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                        <th>Commandes</th>
                                        <th>Inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>
                                                <strong>{{ $client->full_name }}</strong>
                                                @if($client->numero_whatsapp)
                                                    <br><small class="text-muted">WhatsApp: {{ $client->numero_whatsapp }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $client->email }}</td>
                                            <td>{{ $client->numero_telephone }}</td>
                                            <td>
                                                @switch($client->status)
                                                    @case('active')
                                                        <span class="badge badge-success">Actif</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge badge-warning">En attente</span>
                                                        @break
                                                    @case('suspended')
                                                        <span class="badge badge-danger">Suspendu</span>
                                                        @break
                                                    @case('inactive')
                                                        <span class="badge badge-secondary">Inactif</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $client->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $client->orders->count() }}</span>
                                                @if($client->orders->count() > 0)
                                                    <br><small class="text-muted">{{ $client->orders->sum('total_amount') }} FCFA</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $client->created_at->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.users.show', $client) }}"
                                                       class="btn btn-sm btn-info" title="Voir profil">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $client) }}"
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                            data-toggle="modal" data-target="#reassignModal{{ $client->id }}"
                                                            title="Réassigner">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal de réassignation -->
                                                <div class="modal fade" id="reassignModal{{ $client->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Réassigner le client</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('admin.users.reassign-quartier', $client) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <p><strong>Client :</strong> {{ $client->full_name }}</p>
                                                                    <p><strong>Quartier actuel :</strong> {{ $client->quartier->nom ?? 'Non défini' }}</p>

                                                                    <div class="form-group">
                                                                        <label for="quartier_id">Nouveau quartier :</label>
                                                                        <select class="form-control" id="quartier_id" name="quartier_id" required>
                                                                            <option value="">Sélectionner un quartier</option>
                                                                            @foreach(\App\Models\Quartier::active()->ordered()->get() as $q)
                                                                                <option value="{{ $q->id }}"
                                                                                        {{ $client->quartier_id == $q->id ? 'selected' : '' }}>
                                                                                    {{ $q->nom }}, {{ $q->ville }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                    <button type="submit" class="btn btn-primary">Réassigner</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $clients->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun client trouvé</h5>
                            <p class="text-muted">Aucun client ne correspond aux critères de recherche.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
























