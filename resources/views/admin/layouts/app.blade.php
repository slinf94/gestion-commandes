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
            @endphp
            <nav class="nav flex-column">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de Bord</span>
                </a>

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

    @yield('scripts')
</body>
</html>
