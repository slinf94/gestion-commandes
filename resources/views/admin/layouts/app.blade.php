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
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            background: var(--primary-green);
            width: 250px;
            height: 100vh;
            color: var(--white);
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
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
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                width: 280px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .main-header {
                padding: 10px 15px;
            }

            .main-header .header-actions {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
            }

            .main-content {
                padding: 20px;
            }
        }

        /* Bouton hamburger pour mobile */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 2000;
            background: var(--primary-green);
            color: white;
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .sidebar-toggle:hover {
            background: var(--primary-green-dark);
        }

        @media (max-width: 991px) {
            .sidebar-toggle {
                display: block;
            }
        }

    </style>
    @yield('styles')
</head>
<body>
    <!-- Bouton hamburger pour mobile -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
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
                <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 20px; padding-top: 10px;">
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; color: inherit;">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
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

    <!-- Modal de Confirmation Personnalisé -->
    <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-labelledby="customConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customConfirmModalLabel">
                        <i class="fas fa-question-circle me-2"></i>
                        Confirmation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="customConfirmModalBody">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3" id="confirmMessage">
                            Êtes-vous sûr de vouloir effectuer cette action ?
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="customConfirmCancelBtn">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="button" class="btn btn-primary" id="customConfirmOkBtn">
                        <i class="fas fa-check me-1"></i> Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styles pour les notifications modales */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: none;
        }

        .modal-overlay.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #customConfirmModal .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        #customConfirmModal .modal-header {
            background: linear-gradient(135deg, #38B04A, #4CAF50);
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        #customConfirmModal .modal-body {
            padding: 30px;
            font-size: 16px;
        }

        #customConfirmModal .modal-footer {
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
        }

        #customConfirmModal .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        #customConfirmCancelBtn {
            background-color: #6c757d;
            border: none;
        }

        #customConfirmOkBtn {
            background: linear-gradient(135deg, #38B04A, #4CAF50);
            border: none;
        }

        #customConfirmOkBtn:hover {
            background: linear-gradient(135deg, #2d8f3a, #38B04A);
            transform: translateY(-2px);
        }

        .action-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar sur mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const contentArea = document.querySelector('.content-area');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Fermer la sidebar en cliquant sur le contenu (overlay)
                contentArea.addEventListener('click', function() {
                    if (window.innerWidth <= 991) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>

    <script>
        // Système de confirmation personnalisé au centre de l'écran
        let currentConfirmCallback = null;
        let currentConfirmCancelCallback = null;

        // Attendre que le DOM soit chargé
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer le clic sur le bouton OK
            const okBtn = document.getElementById('customConfirmOkBtn');
            if (okBtn) {
                okBtn.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('customConfirmModal'));
                    modal.hide();

                    if (currentConfirmCallback) {
                        currentConfirmCallback();
                    }

                    currentConfirmCallback = null;
                    currentConfirmCancelCallback = null;
                });
            }

            // Gérer le clic sur le bouton Annuler
            const cancelBtn = document.getElementById('customConfirmCancelBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('customConfirmModal'));
                    modal.hide();

                    if (currentConfirmCancelCallback) {
                        currentConfirmCancelCallback();
                    }

                    currentConfirmCallback = null;
                    currentConfirmCancelCallback = null;
                });
            }

            // Gérer la fermeture de la modal avec la croix
            const modalElement = document.getElementById('customConfirmModal');
            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    currentConfirmCallback = null;
                    currentConfirmCancelCallback = null;
                });
            }
        });

        /**
         * Affiche une boîte de dialogue de confirmation personnalisée au centre de l'écran
         * @param {string} message - Le message à afficher
         * @param {function} onConfirm - Callback à exécuter si confirmé
         * @param {function} onCancel - Callback à exécuter si annulé
         * @param {string} title - Titre de la modal (optionnel)
         * @param {string} confirmText - Texte du bouton de confirmation (optionnel)
         * @param {string} cancelText - Texte du bouton d'annulation (optionnel)
         */
        function customConfirm(message, onConfirm, onCancel = null, title = 'Confirmation', confirmText = 'Confirmer', cancelText = 'Annuler') {
            try {
                const modalElement = document.getElementById('customConfirmModal');
                if (!modalElement) {
                    console.error('Modal element not found');
                    return;
                }

                const modal = new bootstrap.Modal(modalElement);
                const modalBody = document.getElementById('confirmMessage');
                const modalTitle = document.getElementById('customConfirmModalLabel');
                const okBtn = document.getElementById('customConfirmOkBtn');
                const cancelBtn = document.getElementById('customConfirmCancelBtn');

                if (!modalBody || !modalTitle || !okBtn || !cancelBtn) {
                    console.error('Modal elements not found');
                    return;
                }

                // Mettre à jour le contenu
                modalTitle.innerHTML = '<i class="fas fa-question-circle me-2"></i> ' + title;
                modalBody.innerHTML = message;
                okBtn.innerHTML = '<i class="fas fa-check me-1"></i> ' + confirmText;
                cancelBtn.innerHTML = '<i class="fas fa-times me-1"></i> ' + cancelText;

                // Stocker les callbacks
                currentConfirmCallback = onConfirm;
                currentConfirmCancelCallback = onCancel;

                // Afficher la modal
                modal.show();
            } catch (error) {
                console.error('Error showing custom confirm:', error);
                alert(message);
            }
        }

        /**
         * Fonction helper pour remplacer les confirm() inline
         * Cette fonction peut être utilisée pour remplacer onclick="return confirm()"
         */
        function confirmDelete(message) {
            return new Promise((resolve) => {
                customConfirm(
                    message,
                    () => resolve(true),
                    () => resolve(false),
                    'Confirmation de suppression',
                    'Supprimer',
                    'Annuler'
                );
            });
        }

        /**
         * Fonction pour gérer les soumissions de formulaires avec confirmation
         * @param {HTMLElement} formElement - L'élément formulaire
         * @param {string} message - Le message de confirmation
         */
        function submitWithConfirmation(formElement, message) {
            customConfirm(
                message,
                () => {
                    // Enlever onsubmit="return false" et soumettre
                    formElement.onsubmit = null;
                    formElement.submit();
                },
                null,
                'Confirmation',
                'Confirmer',
                'Annuler'
            );
            return false;
        }

        /**
         * Fonction pour gérer les suppressions avec confirmation
         * @param {string} url - L'URL de suppression
         * @param {string} message - Le message de confirmation
         * @param {string} method - La méthode HTTP (DELETE par défaut)
         */
        function deleteWithConfirmation(url, message, method = 'DELETE') {
            customConfirm(
                message || 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.',
                () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    if (method !== 'POST') {
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = method;
                        form.appendChild(methodInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                },
                null,
                'Confirmation de suppression',
                'Supprimer',
                'Annuler'
            );
        }

        // Fonction pour afficher des alertes personnalisées
        function showCustomAlert(message, type = 'info', title = 'Information') {
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };

            const colors = {
                'success': '#28a745',
                'error': '#dc3545',
                'warning': '#ffc107',
                'info': '#17a2b8'
            };

            customConfirm(
                message,
                null,
                null,
                `<i class="fas ${icons[type]} me-2"></i> ${title}`,
                'OK',
                'Fermer'
            );
        }
    </script>

    @yield('scripts')
</body>
</html>
