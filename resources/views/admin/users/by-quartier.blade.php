<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients par Quartier - Allo Mobile Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #4CAF50, #2E7D32); min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .main-content { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin: 20px; padding: 30px; }
        .quartier-card { border-left: 4px solid #4CAF50; transition: all 0.3s; }
        .quartier-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-logout { background: #dc3545; border: none; border-radius: 8px; color: white; padding: 8px 15px; }
        .btn-logout:hover { background: #c82333; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Allo Mobile
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tableau de Bord
                        </a>
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i>
                            Utilisateurs
                        </a>
                        <a class="nav-link active" href="{{ route('admin.users.by-quartier') }}">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Par Quartier
                        </a>
                        <a class="nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box me-2"></i>
                            Produits
                        </a>
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Commandes
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h2><i class="fas fa-map-marker-alt me-2"></i>Clients par Quartier</h2>
                    <div>
                        <div class="btn-group me-2" role="group">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.export.by-quartier.csv') }}">
                                        <i class="fas fa-chart-bar me-2"></i>Statistiques par quartier (CSV)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.export.csv') }}">
                                        <i class="fas fa-file-csv me-2"></i>Tous les clients (CSV)
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list"></i> Vue Liste
                        </a>
                        <span class="me-3">Bienvenue, {{ auth()->user()->full_name }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>

                <div class="main-content">
                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">{{ $totalClients }}</h5>
                                    <p class="card-text">Total Clients</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-success">{{ $activeClients }}</h5>
                                    <p class="card-text">Clients Actifs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">{{ $pendingClients }}</h5>
                                    <p class="card-text">En Attente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title text-info">{{ $quartiersAvecClients }}</h5>
                                    <p class="card-text">Quartiers Actifs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des quartiers avec clients -->
                    <div class="row">
                        @foreach($quartiersStats as $quartier)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card quartier-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            {{ $quartier['quartier'] }}
                                        </h6>
                                        <span class="badge bg-primary">{{ $quartier['total_clients'] }}</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center mb-3">
                                            <div class="col-6">
                                                <h6 class="text-success">{{ $quartier['active_clients'] }}</h6>
                                                <small class="text-muted">Actifs</small>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-warning">{{ $quartier['total_clients'] - $quartier['active_clients'] }}</h6>
                                                <small class="text-muted">Autres</small>
                                            </div>
                                        </div>

                                        @if($quartier['total_clients'] > 0)
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div class="progress-bar bg-success"
                                                     style="width: {{ $quartier['total_clients'] > 0 ? ($quartier['active_clients'] / $quartier['total_clients'] * 100) : 0 }}%">
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">
                                                    Taux d'activité: {{ $quartier['total_clients'] > 0 ? round($quartier['active_clients'] / $quartier['total_clients'] * 100, 1) : 0 }}%
                                                </small>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.users.index', ['quartier' => $quartier['quartier']]) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                    @if($quartier['total_clients'] > 0)
                                                        <a href="{{ route('admin.users.export.quartier.csv', $quartier['quartier']) }}"
                                                           class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted text-center mb-0">Aucun client</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Clients sans quartier -->
                    @if($clientsSansQuartier > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Clients sans quartier assigné
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">
                                            <strong>{{ $clientsSansQuartier }}</strong> clients n'ont pas de quartier assigné.
                                        </p>
                                        <a href="{{ route('admin.users.index', ['quartier' => '']) }}"
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Assigner des quartiers
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
