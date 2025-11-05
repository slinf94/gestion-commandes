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

        /* Notifications */
        .notification-icon {
            position: relative;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .notification-icon:hover {
            background-color: var(--bg-light);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            transform: translate(25%, -25%);
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            width: 400px;
            max-height: 500px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            margin-top: 10px;
        }

        .notification-dropdown.show {
            display: block;
        }

        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #e7f3ff;
            border-left: 4px solid var(--primary-green);
        }

        .notification-item.unread:hover {
            background-color: #d0e7ff;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 2px solid var(--primary-green);
            background: var(--primary-green);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .notification-empty {
            padding: 40px;
            text-align: center;
            color: var(--text-light);
        }

        .notification-footer {
            padding: 10px 15px;
            border-top: 1px solid #e9ecef;
            text-align: center;
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

        /* Styles pour la modale d'annulation personnalisée */
        #customCancelOrderModal .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        #customCancelOrderModal .modal-header {
            border-radius: 12px 12px 0 0;
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        #customCancelOrderModal .modal-body {
            padding: 25px;
        }

        #customCancelOrderModal .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        #customCancelOrderModal #cancelReasonInput {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        #customCancelOrderModal #cancelReasonInput:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            outline: none;
        }

        #customCancelOrderModal .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 15px 25px;
        }

        #customCancelOrderModal .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #customCancelOrderModal .btn-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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

            @php
                use App\Helpers\AdminMenuHelper;
                $u = auth()->user();
                $canManageUsers = AdminMenuHelper::canSee($u, 'super-admin', 'admin');
                $canManageProducts = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire', 'vendeur');
                $canManageCategories = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire');
                $canManageClients = AdminMenuHelper::canSee($u, 'super-admin', 'admin', 'gestionnaire');
                $canViewActivityLogs = AdminMenuHelper::canSee($u, 'super-admin', 'admin');
                $canManageSettings = AdminMenuHelper::canSee($u, 'super-admin');
                $canManageRolePermissions = AdminMenuHelper::canSee($u, 'super-admin');
            @endphp
            <nav class="nav flex-column">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de Bord</span>
                </a>

                @if($canManageRolePermissions)
                <a href="{{ route('admin.role-permissions.index') }}" class="nav-link {{ request()->routeIs('admin.role-permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Profils & Droits</span>
                </a>
                @endif

                @if($canManageUsers)
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Utilisateurs</span>
                </a>
                @endif

                @if($canManageProducts)
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Produits</span>
                </a>
                @endif

                @if($canManageCategories)
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
                @endif

                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Commandes</span>
                </a>

                @if($canManageClients)
                <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i>
                    <span>Clients</span>
                </a>
                @endif

                @if($canViewActivityLogs)
                <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Journal des Activités</span>
                </a>
                @endif

                @if($canManageSettings)
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                @endif
            </nav>
        </div>

        <!-- Zone de contenu principal -->
        <div class="content-area">
            <!-- Header -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>@yield('page-title', 'Administration')</h2>
                    <div class="header-actions">
                        <!-- Icône de notification -->
                        <div class="notification-container position-relative">
                            <div class="notification-icon" id="notificationIcon">
                                <i class="fas fa-bell" style="font-size: 20px; color: var(--text-dark);"></i>
                                <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                            </div>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <h6><i class="fas fa-bell me-2"></i>Notifications</h6>
                                    <button class="btn btn-sm btn-light" id="markAllReadBtn" style="display: none;">
                                        <i class="fas fa-check-double me-1"></i>Tout marquer lu
                                    </button>
                                </div>
                                <div id="notificationsList">
                                    <div class="notification-empty">
                                        <i class="fas fa-bell-slash" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                                        <p>Aucune notification</p>
                                    </div>
                                </div>
                                <div class="notification-footer">
                                    <a href="{{ route('admin.notifications.index') }}" id="viewAllNotifications" class="text-primary text-decoration-none">
                                        <i class="fas fa-eye me-1"></i>Voir toutes les notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                        
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

    <!-- Modal de confirmation personnalisée -->
    <div class="modal fade" id="customConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="customConfirmTitle">Confirmation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="customConfirmMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="customConfirmCancel">Annuler</button>
                    <button type="button" class="btn btn-danger" id="customConfirmOk">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'annulation de commande personnalisée -->
    <div class="modal fade" id="customCancelOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="customCancelOrderTitle">Confirmation d'annulation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="customCancelOrderMessage"></p>
                    <div class="mb-3 mt-3">
                        <label for="cancelReasonInput" class="form-label">
                            <i class="fas fa-comment-alt me-2"></i>Raison de l'annulation <small class="text-muted">(optionnel)</small>
                        </label>
                        <textarea 
                            class="form-control" 
                            id="cancelReasonInput" 
                            rows="3" 
                            placeholder="Ex: Annulation par l'administrateur, demande du client, stock indisponible..."
                            style="resize: vertical;"></textarea>
                        <small class="form-text text-muted">
                            Vous pouvez laisser ce champ vide si aucune raison spécifique n'est nécessaire.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="customCancelOrderCancel">Non</button>
                    <button type="button" class="btn btn-danger" id="customCancelOrderOk">
                        <i class="fas fa-times me-2"></i>Oui, annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Fonction utilitaire pour sauvegarder et restaurer le focus dans les champs de recherche
         * Utilisation:
         *   const restoreFocus = saveSearchFocus('search-input-id');
         *   // ... faire l'AJAX ...
         *   restoreFocus();
         */
        function saveSearchFocus(inputId) {
            const activeElement = document.activeElement;
            const input = document.getElementById(inputId);

            // Vérifier si l'input est actuellement focusé
            if (!input || activeElement !== input) {
                return () => {}; // Retourner une fonction vide si pas d'input ou pas focusé
            }

            // Sauvegarder la position du curseur
            const cursorPosition = input.selectionStart;
            const value = input.value;

            console.log(`Focus sauvegardé pour ${inputId} à la position ${cursorPosition}`);

            // Retourner une fonction pour restaurer le focus
            return function() {
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    // Utiliser requestAnimationFrame pour s'assurer que le DOM est mis à jour
                    requestAnimationFrame(() => {
                        inputElement.focus();
                        // S'assurer que la valeur est la même (si elle a changé, restaurer)
                        if (inputElement.value !== value) {
                            inputElement.value = value;
                        }
                        // Restaurer la position du curseur
                        try {
                            inputElement.setSelectionRange(cursorPosition, cursorPosition);
                        } catch (e) {
                            // Si setSelectionRange échoue, placer le curseur à la fin
                            inputElement.setSelectionRange(value.length, value.length);
                        }
                        console.log(`Focus restauré pour ${inputId} à la position ${cursorPosition}`);
                    });
                }
            };
        }

        // Fonction de confirmation personnalisée
        function customConfirm(message, onConfirm, onCancel = null, title = 'Confirmation', okText = 'Confirmer', cancelText = 'Annuler') {
            const modal = new bootstrap.Modal(document.getElementById('customConfirmModal'));
            const modalTitle = document.getElementById('customConfirmTitle');
            const modalMessage = document.getElementById('customConfirmMessage');
            const modalOk = document.getElementById('customConfirmOk');
            const modalCancel = document.getElementById('customConfirmCancel');

            modalTitle.textContent = title;
            modalMessage.innerHTML = message;
            modalOk.textContent = okText;
            modalCancel.textContent = cancelText;

            // Supprimer les anciens event listeners
            const newOkBtn = modalOk.cloneNode(true);
            const newCancelBtn = modalCancel.cloneNode(true);
            modalOk.parentNode.replaceChild(newOkBtn, modalOk);
            modalCancel.parentNode.replaceChild(newCancelBtn, modalCancel);

            // Ajouter les nouveaux event listeners
            newOkBtn.addEventListener('click', function() {
                modal.hide();
                if (onConfirm) onConfirm();
            });

            newCancelBtn.addEventListener('click', function() {
                modal.hide();
                if (onCancel) onCancel();
            });

            modal.show();
        }

        // Fonction d'annulation de commande personnalisée avec champ texte
        function customCancelOrder(orderId, onConfirm, onCancel = null, title = 'Confirmation d\'annulation', okText = 'Oui, annuler', cancelText = 'Non') {
            const modal = new bootstrap.Modal(document.getElementById('customCancelOrderModal'));
            const modalTitle = document.getElementById('customCancelOrderTitle');
            const modalMessage = document.getElementById('customCancelOrderMessage');
            const reasonInput = document.getElementById('cancelReasonInput');
            const modalOk = document.getElementById('customCancelOrderOk');
            const modalCancel = document.getElementById('customCancelOrderCancel');

            modalTitle.textContent = title;
            modalMessage.innerHTML = `Voulez-vous vraiment annuler la commande <strong>#${orderId}</strong> ?<br><small class="text-muted">Vous pouvez saisir une raison d'annulation ci-dessous.</small>`;
            reasonInput.value = 'Annulation par l\'administrateur'; // Valeur par défaut
            modalOk.innerHTML = `<i class="fas fa-times me-2"></i>${okText}`;
            modalCancel.textContent = cancelText;

            // Supprimer les anciens event listeners
            const newOkBtn = modalOk.cloneNode(true);
            const newCancelBtn = modalCancel.cloneNode(true);
            modalOk.parentNode.replaceChild(newOkBtn, modalOk);
            modalCancel.parentNode.replaceChild(newCancelBtn, modalCancel);

            // Ajouter les nouveaux event listeners
            newOkBtn.addEventListener('click', function() {
                const reason = reasonInput.value.trim();
                modal.hide();
                if (onConfirm) onConfirm(reason);
            });

            newCancelBtn.addEventListener('click', function() {
                modal.hide();
                if (onCancel) onCancel();
            });

            // Focus sur le champ texte quand la modale s'affiche
            modal._element.addEventListener('shown.bs.modal', function() {
                reasonInput.focus();
                reasonInput.select(); // Sélectionner le texte par défaut pour faciliter la modification
            });

            // Gérer la touche Entrée pour confirmer
            const handleKeyPress = function(e) {
                if (e.key === 'Enter' && e.ctrlKey) {
                    // Ctrl+Entrée pour confirmer
                    e.preventDefault();
                    const reason = reasonInput.value.trim();
                    modal.hide();
                    if (onConfirm) onConfirm(reason);
                }
            };

            reasonInput.addEventListener('keydown', handleKeyPress);

            // Gérer Escape pour annuler
            modal._element.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    modal.hide();
                    if (onCancel) onCancel();
                }
            });

            modal.show();
        }

        // Fonction d'alerte personnalisée
        function showAlert(message, type = 'info', title = 'Information') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            }[type] || 'alert-info';

            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    <strong>${title}:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.insertAdjacentHTML('afterbegin', alertHtml);
            }
        }

        // Système de notifications
        (function() {
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationBadge = document.getElementById('notificationBadge');
            const notificationsList = document.getElementById('notificationsList');
            const markAllReadBtn = document.getElementById('markAllReadBtn');
            let isOpen = false;
            let refreshInterval = null;

            // Charger les notifications
            function loadNotifications() {
                fetch('{{ route("admin.notifications.api") }}?unread_only=false&per_page=10', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.unread_count);
                        renderNotifications(data.data.data || []);
                    }
                })
                .catch(error => {
                    console.error('Erreur chargement notifications:', error);
                });
            }

            // Charger le nombre de notifications non lues
            function loadUnreadCount() {
                fetch('{{ route("admin.notifications.unread-count") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.count);
                    }
                })
                .catch(error => {
                    console.error('Erreur chargement count:', error);
                });
            }

            // Mettre à jour le badge
            function updateNotificationBadge(count) {
                if (count > 0) {
                    notificationBadge.textContent = count > 99 ? '99+' : count;
                    notificationBadge.style.display = 'flex';
                } else {
                    notificationBadge.style.display = 'none';
                }
            }

            // Rendre les notifications
            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationsList.innerHTML = `
                        <div class="notification-empty">
                            <i class="fas fa-bell-slash" style="font-size: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                            <p>Aucune notification</p>
                        </div>
                    `;
                    markAllReadBtn.style.display = 'none';
                    return;
                }

                const unreadCount = notifications.filter(n => !n.is_read).length;
                markAllReadBtn.style.display = unreadCount > 0 ? 'block' : 'none';

                notificationsList.innerHTML = notifications.map(notification => {
                    const icon = getNotificationIcon(notification.type);
                    const timeAgo = getTimeAgo(notification.created_at);
                    const unreadClass = !notification.is_read ? 'unread' : '';
                    
                    return `
                        <div class="notification-item ${unreadClass}" data-id="${notification.id}" ${!notification.is_read ? 'onclick="markNotificationAsRead(' + notification.id + ')"' : ''}>
                            <div class="d-flex align-items-start">
                                <div class="me-3" style="font-size: 24px; color: var(--primary-green);">
                                    ${icon}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-1">${escapeHtml(notification.title)}</div>
                                    <div class="text-muted small">${escapeHtml(notification.message)}</div>
                                    <div class="text-muted" style="font-size: 11px; margin-top: 5px;">
                                        <i class="fas fa-clock me-1"></i>${timeAgo}
                                    </div>
                                </div>
                                ${!notification.is_read ? '<span class="badge bg-primary">Nouveau</span>' : ''}
                            </div>
                        </div>
                    `;
                }).join('');
            }

            // Obtenir l'icône selon le type
            function getNotificationIcon(type) {
                const icons = {
                    'order': '<i class="fas fa-shopping-bag"></i>',
                    'account': '<i class="fas fa-user"></i>',
                    'client': '<i class="fas fa-users"></i>',
                    'system': '<i class="fas fa-cog"></i>'
                };
                return icons[type] || '<i class="fas fa-bell"></i>';
            }

            // Obtenir le temps écoulé
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = Math.floor((now - date) / 1000);
                
                if (diff < 60) return 'À l\'instant';
                if (diff < 3600) return `Il y a ${Math.floor(diff / 60)} min`;
                if (diff < 86400) return `Il y a ${Math.floor(diff / 3600)} h`;
                if (diff < 604800) return `Il y a ${Math.floor(diff / 86400)} j`;
                return date.toLocaleDateString('fr-FR');
            }

            // Échapper HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Marquer une notification comme lue
            window.markNotificationAsRead = function(id) {
                fetch(`/admin/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        loadUnreadCount();
                    }
                })
                .catch(error => {
                    console.error('Erreur marquage notification:', error);
                });
            };

            // Marquer toutes comme lues
            markAllReadBtn.addEventListener('click', function() {
                fetch('{{ route("admin.notifications.mark-all-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                        loadUnreadCount();
                    }
                })
                .catch(error => {
                    console.error('Erreur marquage toutes:', error);
                });
            });

            // Toggle dropdown
            notificationIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                isOpen = !isOpen;
                if (isOpen) {
                    notificationDropdown.classList.add('show');
                    loadNotifications();
                } else {
                    notificationDropdown.classList.remove('show');
                }
            });

            // Fermer en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                    isOpen = false;
                }
            });

            // Charger au démarrage
            loadUnreadCount();
            
            // Rafraîchir toutes les 30 secondes
            refreshInterval = setInterval(function() {
                loadUnreadCount();
                if (isOpen) {
                    loadNotifications();
                }
            }, 30000);
        })();
    </script>

    <!-- Script de recherche autocomplete -->
    <script>
        /**
         * Script réutilisable pour les champs de recherche avec autocomplete
         */
        (function() {
            'use strict';

            function initializeSearchAutocomplete(input) {
                const wrapper = input.closest('.search-autocomplete-wrapper');
                if (!wrapper) return;

                const resultsContainer = wrapper.querySelector('.search-autocomplete-results');
                const resultsList = wrapper.querySelector('.search-autocomplete-list');
                const emptyMessage = wrapper.querySelector('.search-autocomplete-empty');
                const spinner = wrapper.querySelector('.search-autocomplete-spinner');
                const clearBtn = wrapper.querySelector('.search-autocomplete-clear');
                
                const searchUrl = input.getAttribute('data-search-url');
                const resultKey = input.getAttribute('data-result-key') || 'data';
                const minLength = parseInt(input.getAttribute('data-min-length') || '2');
                const debounceDelay = parseInt(input.getAttribute('data-debounce-delay') || '500');
                
                let debounceTimer = null;
                let isSearching = false;
                let currentFocus = -1;
                let isComposing = false; // Pour gérer les compositions IME (caractères spéciaux)
                
                // Marquer le champ pour éviter les conflits avec d'autres scripts
                input.setAttribute('data-autocomplete-initialized', 'true');
                
                // Fonction pour afficher/masquer le bouton de suppression
                function updateClearButton() {
                    if (clearBtn) {
                        clearBtn.style.display = input.value.length > 0 ? 'block' : 'none';
                    }
                }
                
                // Fonction pour effacer la recherche
                function clearSearch() {
                    input.value = '';
                    input.focus();
                    hideResults();
                    updateClearButton();
                    // Déclencher l'événement input pour mettre à jour le formulaire si nécessaire
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
                
                // Écouter le bouton de suppression
                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        clearSearch();
                    });
                }
                
                // Mettre à jour le bouton au chargement
                updateClearButton();

                function performSearch(query) {
                    if (!searchUrl || query.length < minLength) {
                        hideResults();
                        return;
                    }

                    if (isSearching) return;
                    isSearching = true;

                    // Préserver le focus et la position du curseur AVANT toute opération
                    const wasFocused = document.activeElement === input;
                    const cursorPosition = input.selectionStart;

                    if (spinner) spinner.style.display = 'block';
                    showResults();

                    const url = new URL(searchUrl, window.location.origin);
                    url.searchParams.set('q', query);
                    url.searchParams.set('limit', '10');

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Si la réponse n'est pas OK, essayer de lire le message d'erreur
                            return response.json().then(errData => {
                                throw new Error(errData.message || `HTTP error! status: ${response.status}`);
                            }).catch(() => {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Vérifier si la réponse contient une erreur
                        if (data.success === false) {
                            showError(data.message || 'Erreur lors de la recherche');
                            return;
                        }
                        displayResults(data, resultKey, query);
                    })
                    .catch(error => {
                        console.error('Erreur recherche:', error);
                        showError('Erreur lors de la recherche: ' + (error.message || 'Erreur inconnue'));
                    })
                    .finally(() => {
                        isSearching = false;
                        if (spinner) spinner.style.display = 'none';
                        
                        // Restaurer le focus après la recherche - TRIPLE PROTECTION
                        if (wasFocused) {
                            // Technique 1: Restauration immédiate
                            setTimeout(() => {
                                input.focus();
                                const newCursorPos = Math.min(cursorPosition, input.value.length);
                                input.setSelectionRange(newCursorPos, newCursorPos);
                            }, 0);
                            
                            // Technique 2: Double requestAnimationFrame
                            requestAnimationFrame(() => {
                                requestAnimationFrame(() => {
                                    input.focus();
                                    const newCursorPos = Math.min(cursorPosition, input.value.length);
                                    input.setSelectionRange(newCursorPos, newCursorPos);
                                });
                            });
                            
                            // Technique 3: Vérification après un court délai
                            setTimeout(() => {
                                if (document.activeElement !== input) {
                                    input.focus();
                                    const newCursorPos = Math.min(cursorPosition, input.value.length);
                                    input.setSelectionRange(newCursorPos, newCursorPos);
                                }
                            }, 50);
                        }
                    });
                }

                function displayResults(data, resultKey, query) {
                    if (!resultsList || !resultsContainer) return;
                    const results = getNestedValue(data, resultKey) || [];
                    
                    if (results.length === 0) {
                        showEmptyMessage();
                        showResults(); // Afficher le message "Aucun résultat"
                        return;
                    }

                    hideEmptyMessage();
                    resultsList.innerHTML = '';
                    results.forEach((item, index) => {
                        const itemElement = createResultItem(item, index, query);
                        resultsList.appendChild(itemElement);
                    });
                    currentFocus = -1;
                    
                    // AFFICHER les résultats et LES GARDER AFFICHÉS
                    showResults();
                }

                // Fonction helper pour les badges de statut
                function getStatusBadgeColor(status) {
                    const statusLower = status.toLowerCase();
                    if (statusLower.includes('confirmée') || statusLower.includes('livrée') || statusLower.includes('terminée')) return 'success';
                    if (statusLower.includes('en attente')) return 'warning';
                    if (statusLower.includes('en traitement') || statusLower.includes('expédiée')) return 'info';
                    if (statusLower.includes('annulée')) return 'danger';
                    return 'secondary';
                }

                function createResultItem(item, index, query) {
                    const div = document.createElement('div');
                    div.className = 'search-autocomplete-item';
                    div.setAttribute('data-index', index);
                    div.setAttribute('tabindex', '0');

                    const title = item.title || item.name || item.label || item.text || '';
                    let subtitle = item.subtitle || item.description || item.email || item.phone || '';
                    
                    // Améliorer l'affichage selon le type de résultat
                    if (item.sku) {
                        // C'est un produit, améliorer l'affichage
                        const subtitleParts = subtitle.split(' • ');
                        let formattedSubtitle = '';
                        
                        if (subtitleParts.length >= 2) {
                            // Afficher : Catégorie | Prix | Stock
                            formattedSubtitle = `
                                <div class="search-autocomplete-item-subtitle">
                                    ${subtitleParts[0] ? `<span class="badge bg-info me-2">${escapeHtml(subtitleParts[0])}</span>` : ''}
                                    <strong>${escapeHtml(subtitleParts[1] || '')}</strong>
                                    ${subtitleParts[2] ? `<span class="ms-2 ${subtitleParts[2].includes('Rupture') ? 'text-danger' : 'text-success'}">${escapeHtml(subtitleParts[2])}</span>` : ''}
                                </div>
                            `;
                        } else {
                            formattedSubtitle = subtitle ? `<div class="search-autocomplete-item-subtitle">${escapeHtml(subtitle)}</div>` : '';
                        }
                        
                        div.innerHTML = `
                            <div class="search-autocomplete-item-title">${highlightText(title, query)}</div>
                            ${formattedSubtitle}
                        `;
                    } else if (item.order_number || item.status) {
                        // C'est une commande
                        const subtitleParts = subtitle.split(' • ');
                        let formattedSubtitle = '';
                        
                        if (subtitleParts.length >= 2) {
                            formattedSubtitle = `
                                <div class="search-autocomplete-item-subtitle">
                                    ${subtitleParts[0] ? `<span class="text-primary">${escapeHtml(subtitleParts[0])}</span>` : ''}
                                    <strong class="ms-2">${escapeHtml(subtitleParts[1] || '')}</strong>
                                    ${subtitleParts[2] ? `<span class="badge bg-${getStatusBadgeColor(subtitleParts[2])} ms-2">${escapeHtml(subtitleParts[2])}</span>` : ''}
                                </div>
                            `;
                        } else {
                            formattedSubtitle = subtitle ? `<div class="search-autocomplete-item-subtitle">${escapeHtml(subtitle)}</div>` : '';
                        }
                        
                        div.innerHTML = `
                            <div class="search-autocomplete-item-title">${highlightText(title, query)}</div>
                            ${formattedSubtitle}
                        `;
                    } else {
                        // Autres types de résultats (utilisateurs, catégories, etc.)
                        const subtitleParts = subtitle.split(' • ');
                        let formattedSubtitle = '';
                        
                        if (subtitleParts.length > 1) {
                            formattedSubtitle = `
                                <div class="search-autocomplete-item-subtitle">
                                    ${subtitleParts.map((part, idx) => {
                                        if (idx === 0 && item.email) {
                                            return `<span class="text-primary">${escapeHtml(part)}</span>`;
                                        } else if (part.includes('Actif') || part.includes('Inactif')) {
                                            const color = part.includes('Actif') ? 'success' : 'secondary';
                                            return `<span class="badge bg-${color} ms-2">${escapeHtml(part)}</span>`;
                                        } else {
                                            return `<span>${escapeHtml(part)}</span>`;
                                        }
                                    }).join(' • ')}
                                </div>
                            `;
                        } else {
                            formattedSubtitle = subtitle ? `<div class="search-autocomplete-item-subtitle">${escapeHtml(subtitle)}</div>` : '';
                        }
                        
                        div.innerHTML = `
                            <div class="search-autocomplete-item-title">${highlightText(title, query)}</div>
                            ${formattedSubtitle}
                        `;
                    }

                    div.addEventListener('mousedown', function(e) {
                        // Empêcher la perte de focus lors du clic
                        e.preventDefault();
                        selectItem(item);
                    });
                    
                    div.addEventListener('click', () => selectItem(item));
                    
                    div.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            selectItem(item);
                        }
                    });

                    return div;
                }

                function highlightText(text, query) {
                    if (!text || !query) return escapeHtml(text);
                    const escapedText = escapeHtml(text);
                    const escapedQuery = escapeHtml(query);
                    const regex = new RegExp(`(${escapedQuery})`, 'gi');
                    return escapedText.replace(regex, '<mark>$1</mark>');
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                function selectItem(item) {
                    // Si l'élément a une URL, rediriger vers cette URL
                    if (item.url) {
                        window.location.href = item.url;
                        return;
                    }

                    // Sinon, remplir le champ avec la valeur sélectionnée
                    if (item.value !== undefined) {
                        input.value = item.value;
                    } else if (item.title) {
                        input.value = item.title;
                    } else if (item.name) {
                        input.value = item.name;
                    }

                    input.dispatchEvent(new CustomEvent('search:selected', {
                        detail: { item: item }
                    }));

                    // Masquer les résultats seulement après sélection
                    hideResults();
                    
                    // Restaurer le focus avec position du curseur
                    setTimeout(() => {
                        input.focus();
                        const cursorPos = input.value.length;
                        input.setSelectionRange(cursorPos, cursorPos);
                    }, 0);
                }

                function getNestedValue(obj, path) {
                    return path.split('.').reduce((current, key) => current && current[key], obj);
                }

                function showResults() {
                    if (resultsContainer) {
                        resultsContainer.style.display = 'block';
                        resultsContainer.style.visibility = 'visible';
                        resultsContainer.style.opacity = '1';
                        // S'assurer que le conteneur reste visible
                        resultsContainer.setAttribute('data-visible', 'true');
                    }
                }

                function hideResults() {
                    // Ne masquer que si l'utilisateur clique explicitement en dehors ou efface le texte
                    if (resultsContainer) {
                        resultsContainer.style.display = 'none';
                        resultsContainer.style.visibility = 'hidden';
                        resultsContainer.setAttribute('data-visible', 'false');
                        currentFocus = -1;
                    }
                }

                function showEmptyMessage() {
                    if (emptyMessage) emptyMessage.style.display = 'block';
                    if (resultsList) resultsList.innerHTML = '';
                }

                function hideEmptyMessage() {
                    if (emptyMessage) emptyMessage.style.display = 'none';
                }

                function showError(message) {
                    // Afficher l'erreur dans le conteneur de résultats
                    if (resultsContainer) {
                        resultsContainer.style.display = 'block';
                        if (emptyMessage) {
                            emptyMessage.style.display = 'none';
                        }
                        if (resultsList) {
                            resultsList.innerHTML = `
                                <div style="padding: 20px; text-align: center; color: #dc3545; border-bottom: 1px solid #f0f0f0;">
                                    <i class="fas fa-exclamation-circle me-2"></i><strong>${escapeHtml(message)}</strong>
                                </div>
                            `;
                        }
                    }
                }

                function setActiveItem(items, index) {
                    items.forEach((item, i) => {
                        if (i === index) {
                            item.classList.add('active');
                            item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                        } else {
                            item.classList.remove('active');
                        }
                    });
                }

                // Gérer les événements de composition (pour les caractères spéciaux)
                input.addEventListener('compositionstart', function() {
                    isComposing = true;
                });
                
                input.addEventListener('compositionend', function() {
                    isComposing = false;
                });

                // Fonction pour préserver et restaurer le focus
                function preserveFocus(callback) {
                    const wasFocused = document.activeElement === input;
                    const cursorPosition = input.selectionStart;
                    
                    // Exécuter la callback
                    if (callback) callback();
                    
                    // Toujours restaurer le focus si le champ était focusé
                    if (wasFocused) {
                        // Utiliser requestAnimationFrame pour garantir que le DOM est stable
                        requestAnimationFrame(() => {
                            input.focus();
                            const newCursorPos = Math.min(cursorPosition, input.value.length);
                            input.setSelectionRange(newCursorPos, newCursorPos);
                        });
                    }
                }

                input.addEventListener('input', function(e) {
                    // Ne pas traiter pendant la composition (caractères spéciaux)
                    if (isComposing) return;
                    
                    // Empêcher la propagation pour éviter les conflits avec d'autres scripts
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // PRÉSERVER IMMÉDIATEMENT la position du curseur AVANT toute autre opération
                    const cursorPosition = input.selectionStart || input.value.length;
                    const wasFocused = document.activeElement === input;
                    
                    // Mettre à jour le bouton de suppression
                    updateClearButton();
                    
                    const query = e.target.value.trim();
                    clearTimeout(debounceTimer);
                    
                    // RESTAURER LE FOCUS IMMÉDIATEMENT après chaque frappe - TRIPLE PROTECTION
                    if (wasFocused) {
                        // Technique 1: Restauration immédiate
                        setTimeout(() => {
                            input.focus();
                            const newCursorPos = Math.min(cursorPosition, input.value.length);
                            input.setSelectionRange(newCursorPos, newCursorPos);
                        }, 0);
                        
                        // Technique 2: Double requestAnimationFrame pour garantir
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                input.focus();
                                const newCursorPos = Math.min(cursorPosition, input.value.length);
                                input.setSelectionRange(newCursorPos, newCursorPos);
                            });
                        });
                        
                        // Technique 3: Vérification supplémentaire après un court délai
                        setTimeout(() => {
                            if (document.activeElement !== input) {
                                input.focus();
                                const newCursorPos = Math.min(cursorPosition, input.value.length);
                                input.setSelectionRange(newCursorPos, newCursorPos);
                            }
                        }, 10);
                    }
                    
                    if (query.length === 0) {
                        hideResults();
                        return;
                    }

                    // Vérifier si l'autocomplete est activé et si une URL de recherche existe
                    if (!searchUrl || searchUrl === '') {
                        return; // Pas d'autocomplete, le champ fonctionne comme un champ de recherche normal
                    }

                    if (query.length < minLength) {
                        hideResults();
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        // Préserver le focus AVANT la recherche
                        const currentCursorPos = input.selectionStart || input.value.length;
                        const wasFocusedBefore = document.activeElement === input;
                        const inputValue = input.value; // Sauvegarder la valeur
                        
                        performSearch(query);
                        
                        // Restaurer le focus APRÈS la recherche - TRIPLE PROTECTION
                        if (wasFocusedBefore) {
                            // Technique 1: Restauration immédiate
                            setTimeout(() => {
                                input.focus();
                                const newCursorPos = Math.min(currentCursorPos, input.value.length);
                                input.setSelectionRange(newCursorPos, newCursorPos);
                            }, 0);
                            
                            // Technique 2: Double requestAnimationFrame
                            requestAnimationFrame(() => {
                                requestAnimationFrame(() => {
                                    input.focus();
                                    const newCursorPos = Math.min(currentCursorPos, input.value.length);
                                    input.setSelectionRange(newCursorPos, newCursorPos);
                                });
                            });
                            
                            // Technique 3: Vérification après un court délai
                            setTimeout(() => {
                                if (document.activeElement !== input) {
                                    input.focus();
                                    const newCursorPos = Math.min(currentCursorPos, input.value.length);
                                    input.setSelectionRange(newCursorPos, newCursorPos);
                                }
                            }, 50);
                        }
                    }, debounceDelay);
                });
                
                // Empêcher la perte de focus lors des clics sur les résultats
                // NE PAS masquer les résultats lors du blur - ils restent affichés
                input.addEventListener('blur', function(e) {
                    // Ne pas perdre le focus si on clique sur les résultats ou le wrapper
                    const relatedTarget = e.relatedTarget;
                    const cursorPos = input.selectionStart || input.value.length;
                    
                    if (relatedTarget && wrapper.contains(relatedTarget)) {
                        // Utiliser setTimeout pour restaurer le focus après le blur
                        setTimeout(() => {
                            input.focus();
                            input.setSelectionRange(cursorPos, cursorPos);
                        }, 0);
                        
                        // Double protection avec requestAnimationFrame
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                input.focus();
                                input.setSelectionRange(cursorPos, cursorPos);
                            });
                        });
                    }
                    // NE PAS appeler hideResults() ici - les résultats restent affichés
                });

                // Gérer les clics pour maintenir le focus ET garder les résultats affichés
                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) {
                        // Clic en dehors du wrapper - masquer les résultats
                        hideResults();
                    } else if (e.target === input || wrapper.contains(e.target)) {
                        // Si on clique sur l'input ou dans le wrapper, s'assurer qu'il a le focus
                        const cursorPos = input.selectionStart || input.value.length;
                        
                        // Si on clique sur l'input, afficher les résultats s'ils existent
                        if (e.target === input && input.value.length >= minLength) {
                            // Afficher les résultats si on a déjà fait une recherche
                            if (resultsList && resultsList.children.length > 0) {
                                showResults();
                            }
                        }
                        
                        // Triple protection pour le focus
                        setTimeout(() => {
                            input.focus();
                            input.setSelectionRange(cursorPos, cursorPos);
                        }, 0);
                        
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                if (document.activeElement !== input) {
                                    input.focus();
                                    input.setSelectionRange(cursorPos, cursorPos);
                                }
                            });
                        });
                    }
                });
                
                // Empêcher la perte de focus lors de l'affichage des résultats
                input.addEventListener('focus', function() {
                    // S'assurer que le focus reste avec triple protection
                    const cursorPos = input.selectionStart || input.value.length;
                    
                    setTimeout(() => {
                        input.focus();
                        input.setSelectionRange(cursorPos, cursorPos);
                    }, 0);
                    
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            if (document.activeElement !== input) {
                                input.focus();
                                input.setSelectionRange(cursorPos, cursorPos);
                            }
                        });
                    });
                });
                
                // Gérer les événements mousedown pour préserver le focus
                input.addEventListener('mousedown', function(e) {
                    // Ne pas empêcher le comportement par défaut, mais s'assurer que le focus reste
                    const cursorPos = input.selectionStart || input.value.length;
                    
                    setTimeout(() => {
                        input.focus();
                        input.setSelectionRange(cursorPos, cursorPos);
                    }, 0);
                    
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            input.focus();
                            input.setSelectionRange(cursorPos, cursorPos);
                        });
                    });
                });
                
                // Gérer les événements keydown pour préserver le focus ET navigation
                input.addEventListener('keydown', function(e) {
                    const items = resultsList ? resultsList.querySelectorAll('.search-autocomplete-item') : [];
                    
                    // Navigation dans les résultats (ArrowDown, ArrowUp, Enter, Escape)
                    if (items.length > 0 && ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'].includes(e.key)) {
                        // La navigation est gérée plus bas
                    } else {
                        // Pour toutes les autres touches, préserver le focus
                        const cursorPos = input.selectionStart || input.value.length;
                        setTimeout(() => {
                            input.focus();
                            input.setSelectionRange(cursorPos, cursorPos);
                        }, 0);
                    }
                    
                    if (items.length === 0) return;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        currentFocus = (currentFocus < items.length - 1) ? currentFocus + 1 : 0;
                        setActiveItem(items, currentFocus);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        currentFocus = (currentFocus > 0) ? currentFocus - 1 : items.length - 1;
                        setActiveItem(items, currentFocus);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (currentFocus >= 0 && items[currentFocus]) {
                            items[currentFocus].click();
                        }
                    } else if (e.key === 'Escape') {
                        hideResults();
                    }
                });
            }

            // Initialiser tous les champs de recherche au chargement
            function initAllSearchInputs() {
                const searchInputs = document.querySelectorAll('.search-autocomplete-input:not([data-autocomplete-initialized])');
                searchInputs.forEach(input => {
                    try {
                        initializeSearchAutocomplete(input);
                    } catch (error) {
                        console.error('Erreur initialisation autocomplete:', error);
                    }
                });
            }
            
            // Initialiser au chargement du DOM
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAllSearchInputs);
            } else {
                // DOM déjà chargé
                initAllSearchInputs();
            }
            
            // Réinitialiser après les mises à jour AJAX (MutationObserver)
            const observer = new MutationObserver(function(mutations) {
                let shouldReinit = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1 && (node.classList.contains('search-autocomplete-input') || node.querySelector('.search-autocomplete-input'))) {
                                shouldReinit = true;
                            }
                        });
                    }
                });
                if (shouldReinit) {
                    initAllSearchInputs();
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        })();
    </script>

    @yield('scripts')
</body>
</html>
