<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Allo Mobile Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table thead {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
        }
        .btn-logout {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            color: white;
            padding: 8px 15px;
        }
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
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
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tableau de Bord
                        </a>
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i>
                            Utilisateurs
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
                    <h2>Tableau de Bord</h2>
                    <div>
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
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number">{{ $stats['total_users'] }}</div>
                                        <div>Utilisateurs</div>
                                    </div>
                                    <i class="fas fa-users stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number">{{ $stats['total_products'] }}</div>
                                        <div>Produits</div>
                                    </div>
                                    <i class="fas fa-box stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number">{{ $stats['total_orders'] }}</div>
                                        <div>Commandes</div>
                                    </div>
                                    <i class="fas fa-shopping-bag stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</div>
                                        <div>Chiffre d'Affaires</div>
                                    </div>
                                    <i class="fas fa-chart-line stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Commandes récentes -->
                        <div class="col-md-8">
                            <h5 class="mb-3">
                                <i class="fas fa-clock me-2"></i>
                                Commandes Récentes
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Statut</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->full_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'delivered' ? 'success' : 'info') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucune commande récente</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Produits populaires -->
                        <div class="col-md-4">
                            <h5 class="mb-3">
                                <i class="fas fa-star me-2"></i>
                                Produits Populaires
                            </h5>
                            <div class="list-group">
                                @forelse($top_products as $product)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ number_format($product->price, 0, ',', ' ') }} FCFA</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $product->total_sold }}</span>
                                </div>
                                @empty
                                <div class="list-group-item text-center">Aucun produit vendu</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
