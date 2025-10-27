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
            --primary-green: #38B04A;
            --primary-green-dark: #2d8f3a;
            --primary-green-light: #4CAF50;
            --secondary-green: #66BB6A;
            --accent-green: #A5D6A7;
            --text-dark: #2E2E2E;
            --text-light: #757575;
            --bg-light: #F8F9FA;
            --white: #FFFFFF;
            --shadow: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-hover: 0 5px 15px rgba(0,0,0,0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Layout principal */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: var(--primary-green);
            width: 250px;
            min-height: 100vh;
            color: var(--white);
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: var(--white);
            padding: 15px 20px;
            margin: 2px 0;
            transition: all 0.3s ease;
            border: none;
            border-radius: 0;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link span {
            font-size: 14px;
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }

        /* Zone de contenu */
        .content-area {
            flex: 1;
            margin-left: 250px;
            background: var(--bg-light);
            min-height: 100vh;
            width: calc(100% - 250px);
            position: relative;
            z-index: 1;
        }

        /* Header */
        .main-header {
            background: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 30px;
            margin-bottom: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #e9ecef;
        }

        .main-header h2 {
            color: var(--primary-green-dark);
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }

        .main-header .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Contenu principal */
        .main-content {
            background: var(--white);
            padding: 30px;
            min-height: calc(100vh - 200px);
        }

        /* Boutons */
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

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 8px;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green-light));
            border: none;
            border-radius: 8px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .card-header {
            background: var(--primary-green);
            color: var(--white);
            border-radius: 12px 12px 0 0 !important;
            padding: 15px 20px;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--bg-light);
            border-top: none;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Badges */
        .badge {
            font-size: 0.75em;
            padding: 0.5em 0.75em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
                width: 100%;
            }

            .main-header {
                padding: 10px 15px;
            }

            .main-header .header-actions {
                flex-direction: column;
                gap: 10px;
                align-items: flex-end;
            }

            .main-content {
                padding: 15px;
            }
        }

    </style>
    @yield('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-shopping-cart me-2"></i>
                Allo Mobile
            </div>

            <nav class="nav flex-column">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de Bord</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Catégories</span>
                </a>
                <a href="{{ route('admin.attributes.index') }}" class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Attributs</span>
                </a>
                <a href="{{ route('admin.product-types.index') }}" class="nav-link {{ request()->routeIs('admin.product-types.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Types de Produits</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Commandes</span>
                </a>
                <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i>
                    <span>Clients</span>
                </a>
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Journal des Activités</span>
                </a>
            </nav>
        </div>

        <!-- Zone de contenu principal -->
        <div class="content-area">
            <!-- Header -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>@yield('page-title', 'Administration')</h2>
                    <div class="header-actions">
                        <span class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Bienvenue, {{ auth()->user()->prenom ?? 'Admin' }}
                        </span>
                        <a href="{{ route('admin.users.by-quartier') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Par Quartier
                        </a>
                        @if(request()->routeIs('admin.users.*'))
                            <a href="{{ route('admin.users.create') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Nouvel Utilisateur
                            </a>
                        @endif
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
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

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
