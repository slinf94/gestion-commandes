<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Allo Mobile Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: #4CAF50;
            --primary-green-dark: #2E7D32;
            --primary-green-light: #81C784;
            --secondary-green: #66BB6A;
            --accent-green: #A5D6A7;
            --text-dark: #2E2E2E;
            --text-light: #757575;
            --bg-light: #F8F9FA;
            --white: #FFFFFF;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 5px 15px rgba(0,0,0,0.15);
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        /* Sidebar uniforme */
        .sidebar {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            min-height: 100vh;
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: var(--white);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: var(--white);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Header uniforme */
        .main-header {
            background: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 30px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .main-header h2 {
            color: var(--primary-green-dark);
            margin: 0;
            font-weight: 600;
        }

        /* Contenu principal */
        .main-content {
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 200px);
        }

        /* Boutons uniformes */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-green-dark), var(--primary-green));
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green-light));
            border: none;
            border-radius: 8px;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        }

        .btn-outline-primary {
            color: var(--primary-green);
            border-color: var(--primary-green);
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background: var(--primary-green);
            border-color: var(--primary-green);
        }

        /* Tables uniformes */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: var(--white);
        }

        .table thead th {
            border: none;
            font-weight: 600;
            padding: 15px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(76, 175, 80, 0.05);
            transform: scale(1.01);
        }

        /* Cards uniformes */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
            color: var(--white);
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }

        /* Badges uniformes */
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
            font-weight: 500;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green-light)) !important;
        }

        .badge.bg-warning {
            background: linear-gradient(135deg, #FF9800, #F57C00) !important;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f) !important;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, #2196F3, #1976D2) !important;
        }

        /* Bouton de déconnexion */
        .btn-logout {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            border: none;
            border-radius: 8px;
            color: var(--white);
            padding: 8px 15px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* Alertes uniformes */
        .alert-success {
            background: linear-gradient(135deg, var(--accent-green), var(--primary-green-light));
            border: none;
            color: var(--primary-green-dark);
            border-radius: 10px;
        }

        /* Formulaires uniformes */
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #E0E0E0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        /* Pagination uniforme */
        .pagination .page-link {
            color: var(--primary-green);
            border-color: var(--primary-green-light);
            border-radius: 8px;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-green);
            border-color: var(--primary-green);
        }

        .pagination .page-link:hover {
            background: var(--primary-green-light);
            border-color: var(--primary-green);
            color: var(--white);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }

            .main-content {
                margin: 10px;
                padding: 20px;
            }

            .main-header {
                padding: 15px 20px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar uniforme -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Allo Mobile
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="ms-2">Tableau de Bord</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span class="ms-2">Utilisateurs</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box"></i>
                            <span class="ms-2">Produits</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="ms-2">Commandes</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.clients.index') }}">
                            <i class="fas fa-user-friends"></i>
                            <span class="ms-2">Clients</span>
                        </a>
                        <a class="nav-link {{ Request::routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
                            <i class="fas fa-history me-2"></i>
                            <span class="ms-2">Journal des Activités</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-md-9 col-lg-10">
                <!-- Header uniforme -->
                <div class="main-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>@yield('page-title', 'Administration')</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted">
                                <i class="fas fa-user-circle me-1"></i>
                                Bienvenue, {{ auth()->user()->full_name }}
                            </span>
                            <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-logout">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contenu de la page -->
                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
