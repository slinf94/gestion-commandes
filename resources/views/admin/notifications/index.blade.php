@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('page-title', 'Mes Notifications')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #38B04A, #4CAF50); color: white;">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Mes Notifications</h5>
        <div>
            <button class="btn btn-sm btn-light" id="refreshBtn">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Badges de comptage par type -->
        <div class="row mb-3" id="notificationBadges" style="display: none;">
            <div class="col-md-4 mb-2">
                <div class="card border-primary">
                    <div class="card-body text-center p-2">
                        <h6 class="mb-1 text-primary">
                            <i class="fas fa-mobile-alt me-1"></i>Téléphones
                        </h6>
                        <div>
                            <span class="badge bg-primary" id="telephonesBadge">0</span>
                            <small class="text-muted ms-2">
                                (<span id="telephonesUnreadBadge" class="text-warning">0</span> non lues)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-info">
                    <div class="card-body text-center p-2">
                        <h6 class="mb-1 text-info">
                            <i class="fas fa-headphones me-1"></i>Accessoires
                        </h6>
                        <div>
                            <span class="badge bg-info" id="accessoiresBadge">0</span>
                            <small class="text-muted ms-2">
                                (<span id="accessoiresUnreadBadge" class="text-warning">0</span> non lues)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-secondary">
                    <div class="card-body text-center p-2">
                        <h6 class="mb-1 text-secondary">
                            <i class="fas fa-list me-1"></i>Autres
                        </h6>
                        <div>
                            <span class="badge bg-secondary" id="otherBadge">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sections de notifications -->
        <div id="notificationsContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-3 text-muted">Chargement des notifications...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .notification-card {
        border-left: 4px solid #38B04A;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .notification-card.unread {
        background-color: #e7f3ff;
        border-left-color: #2196F3;
    }

    .notification-card.read {
        background-color: #f8f9fa;
        border-left-color: #6c757d;
    }

    .notification-icon {
        font-size: 24px;
        color: #38B04A;
    }

    .notification-time {
        font-size: 12px;
        color: #6c757d;
    }

    .notification-actions {
        display: flex;
        gap: 10px;
    }

    .notification-empty {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .notification-empty i {
        font-size: 64px;
        opacity: 0.3;
        margin-bottom: 20px;
    }

    .notification-section {
        margin-bottom: 20px;
    }

    .notification-section .card-header {
        font-weight: 600;
    }

    .notification-section .list-group-item {
        border: none;
        border-left: 3px solid;
        transition: all 0.2s ease;
    }

    .notification-section .list-group-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .notification-section .list-group-item.unread {
        border-left-color: #2196F3;
        background-color: #e7f3ff;
    }

    .notification-section .list-group-item.read {
        border-left-color: #6c757d;
        background-color: #f8f9fa;
    }

    #notificationBadges .card {
        transition: transform 0.2s ease;
    }

    #notificationBadges .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('scripts')
<script>
// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔔 DOM chargé - Initialisation du script de notifications');
    
    const notificationsContainer = document.getElementById('notificationsContainer');
    // const markAllReadBtn = document.getElementById('markAllReadBtn'); // Masqué - ne pas supprimer la logique
    const refreshBtn = document.getElementById('refreshBtn');
    
    if (!notificationsContainer) {
        console.error('❌ notificationsContainer non trouvé');
        return;
    }
    
    // markAllReadBtn masqué - ne pas supprimer la logique
    // if (!markAllReadBtn) {
    //     console.error('❌ markAllReadBtn non trouvé dans le DOM');
    // } else {
    //     console.log('✅ markAllReadBtn trouvé:', markAllReadBtn);
    // }
    
    if (!refreshBtn) {
        console.error('❌ refreshBtn non trouvé');
    }
    
    let currentPage = 1;
    let isLoading = false;

    // Charger les notifications
    function loadNotifications(page = 1, append = false) {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            notificationsContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3 text-muted">Chargement des notifications...</p>
                </div>
            `;
        }

        fetch(`{{ route('admin.notifications.api') }}?per_page=20&page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            isLoading = false;
            if (data.success) {
                renderNotifications(data.data.data || [], append);
                currentPage = data.data.current_page || 1;
                
                // Mettre à jour les badges avec les comptes globaux si disponibles
                if (data.counts_by_type) {
                    updateBadgesFromCounts(data.counts_by_type, data.unread_counts_by_type || {});
                }
            } else {
                showError(data.message || 'Erreur lors du chargement des notifications');
            }
        })
        .catch(error => {
            isLoading = false;
            console.error('Erreur détaillée:', error);
            showError('Erreur lors du chargement des notifications: ' + error.message);
        });
    }

    // Rendre les notifications
    function renderNotifications(notifications, append = false) {
        if (!append) {
            notificationsContainer.innerHTML = '';
        }

        if (notifications.length === 0 && !append) {
            notificationsContainer.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <h5>Aucune notification</h5>
                    <p>Vous n'avez pas encore de notifications.</p>
                </div>
            `;
            document.getElementById('notificationBadges').style.display = 'none';
            return;
        }

        // Séparer les notifications par type
        // Les notifications "mixed" apparaissent dans les deux sections
        const telephonesNotifications = notifications.filter(n => 
            n.product_type === 'telephone' || 
            (n.product_type === 'mixed' && n.has_telephones) ||
            (n.type === 'order' && n.has_telephones)
        );
        const accessoiresNotifications = notifications.filter(n => 
            n.product_type === 'accessoire' || 
            (n.product_type === 'mixed' && n.has_accessoires) ||
            (n.type === 'order' && n.has_accessoires)
        );
        const otherNotifications = notifications.filter(n => 
            (!n.product_type || n.product_type === 'other') && 
            n.type !== 'order' &&
            !n.has_telephones && 
            !n.has_accessoires
        );

        // Mettre à jour les badges
        updateBadges(notifications);

        // Afficher les sections
        if (!append) {
            notificationsContainer.innerHTML = '';
        }

        // Section Téléphones
        if (telephonesNotifications.length > 0) {
            const section = createNotificationSection('Téléphones', 'telephones', telephonesNotifications);
            notificationsContainer.appendChild(section);
        }

        // Section Accessoires
        if (accessoiresNotifications.length > 0) {
            const section = createNotificationSection('Accessoires', 'accessoires', accessoiresNotifications);
            notificationsContainer.appendChild(section);
        }

        // Section Autres
        if (otherNotifications.length > 0) {
            const section = createNotificationSection('Autres Notifications', 'other', otherNotifications);
            notificationsContainer.appendChild(section);
        }
    }

    // Créer une section de notifications
    function createNotificationSection(title, type, notifications) {
        const section = document.createElement('div');
        section.className = 'notification-section mb-4';
        section.id = `section-${type}`;
        
        const icon = type === 'telephones' ? '<i class="fas fa-mobile-alt"></i>' : 
                     type === 'accessoires' ? '<i class="fas fa-headphones"></i>' : 
                     '<i class="fas fa-bell"></i>';
        const colorClass = type === 'telephones' ? 'primary' : 
                          type === 'accessoires' ? 'info' : 
                          'secondary';
        
        const unreadCount = notifications.filter(n => !n.is_read).length;
        
        section.innerHTML = `
            <div class="card border-${colorClass} mb-3">
                <div class="card-header bg-${colorClass} bg-opacity-10">
                    <h6 class="mb-0 text-${colorClass}">
                        ${icon} ${title}
                        <span class="badge bg-${colorClass} ms-2">${notifications.length}</span>
                        ${unreadCount > 0 ? `<span class="badge bg-warning ms-2">${unreadCount} non lue(s)</span>` : ''}
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="notifications-${type}">
                    </div>
                </div>
            </div>
        `;
        
        const listContainer = section.querySelector(`#notifications-${type}`);
        notifications.forEach(notification => {
            const card = createNotificationCard(notification);
            listContainer.appendChild(card);
        });
        
        return section;
    }

    // Mettre à jour les badges depuis les notifications affichées
    function updateBadges(notifications) {
        const telephonesCount = notifications.filter(n => 
            n.product_type === 'telephone' || 
            (n.product_type === 'mixed' && n.has_telephones) ||
            (n.type === 'order' && n.has_telephones)
        ).length;
        const accessoiresCount = notifications.filter(n => 
            n.product_type === 'accessoire' || 
            (n.product_type === 'mixed' && n.has_accessoires) ||
            (n.type === 'order' && n.has_accessoires)
        ).length;
        const otherCount = notifications.filter(n => 
            (!n.product_type || n.product_type === 'other') && 
            n.type !== 'order' &&
            !n.has_telephones && 
            !n.has_accessoires
        ).length;
        
        const telephonesUnread = notifications.filter(n => 
            (n.product_type === 'telephone' || 
             (n.product_type === 'mixed' && n.has_telephones) ||
             (n.type === 'order' && n.has_telephones)) && 
            !n.is_read
        ).length;
        const accessoiresUnread = notifications.filter(n => 
            (n.product_type === 'accessoire' || 
             (n.product_type === 'mixed' && n.has_accessoires) ||
             (n.type === 'order' && n.has_accessoires)) && 
            !n.is_read
        ).length;
        
        document.getElementById('telephonesBadge').textContent = telephonesCount;
        document.getElementById('telephonesUnreadBadge').textContent = telephonesUnread;
        document.getElementById('accessoiresBadge').textContent = accessoiresCount;
        document.getElementById('accessoiresUnreadBadge').textContent = accessoiresUnread;
        document.getElementById('otherBadge').textContent = otherCount;
        
        document.getElementById('notificationBadges').style.display = 'flex';
    }

    // Mettre à jour les badges depuis les comptes globaux (si disponibles)
    function updateBadgesFromCounts(countsByType, unreadCountsByType) {
        if (countsByType) {
            document.getElementById('telephonesBadge').textContent = countsByType.telephones || 0;
            document.getElementById('accessoiresBadge').textContent = countsByType.accessoires || 0;
            document.getElementById('otherBadge').textContent = (countsByType.other || 0) + (countsByType.mixed || 0);
            
            if (unreadCountsByType) {
                document.getElementById('telephonesUnreadBadge').textContent = unreadCountsByType.telephones || 0;
                document.getElementById('accessoiresUnreadBadge').textContent = unreadCountsByType.accessoires || 0;
            }
            
            document.getElementById('notificationBadges').style.display = 'flex';
        }
    }

    // Créer une carte de notification
    function createNotificationCard(notification) {
        const card = document.createElement('div');
        card.className = `list-group-item ${notification.is_read ? 'read' : 'unread'}`;
        card.dataset.id = notification.id;
        card.style.borderLeft = notification.is_read ? '3px solid #6c757d' : '3px solid #2196F3';
        card.style.backgroundColor = notification.is_read ? '#f8f9fa' : '#e7f3ff';
        card.style.marginBottom = '5px';
        card.style.borderRadius = '4px';
        
        const icon = getNotificationIcon(notification.type);
        const timeAgo = getTimeAgo(notification.created_at);
        
        // Badge de type de produit
        let productTypeBadge = '';
        if (notification.product_type === 'telephone') {
            productTypeBadge = '<span class="badge bg-primary me-1"><i class="fas fa-mobile-alt"></i> Téléphone</span>';
        } else if (notification.product_type === 'accessoire') {
            productTypeBadge = '<span class="badge bg-info me-1"><i class="fas fa-headphones"></i> Accessoire</span>';
        } else if (notification.product_type === 'mixed') {
            productTypeBadge = '<span class="badge bg-warning me-1"><i class="fas fa-layer-group"></i> Mixte</span>';
        }
        
        card.innerHTML = `
            <div class="d-flex align-items-start p-2">
                <div class="me-3">
                    <div class="notification-icon">${icon}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 fw-bold">${escapeHtml(notification.title)}</h6>
                            ${productTypeBadge}
                        </div>
                        ${!notification.is_read ? '<span class="badge bg-primary">Nouveau</span>' : ''}
                    </div>
                    <p class="mb-2 text-muted small">${escapeHtml(notification.message)}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="notification-time small text-muted">
                            <i class="fas fa-clock me-1"></i>${timeAgo}
                        </span>
                        <div class="notification-actions">
                            ${!notification.is_read ? `
                                <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(${notification.id})">
                                    <i class="fas fa-check me-1"></i>Marquer lu
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;

        return card;
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
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Échapper HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Marquer comme lue
    window.markAsRead = function(id) {
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
                const card = document.querySelector(`[data-id="${id}"]`);
                if (card) {
                    card.classList.remove('unread');
                    card.classList.add('read');
                    const badge = card.querySelector('.badge');
                    if (badge) badge.remove();
                    const markBtn = card.querySelector('button[onclick*="markAsRead"]');
                    if (markBtn) markBtn.remove();
                }
                showAlert('Notification marquée comme lue', 'success');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('Erreur lors du marquage', 'error');
        });
    };

    // Supprimer notification
    window.deleteNotification = function(id) {
        // Récupérer les détails de la notification pour le message personnalisé
        const card = document.querySelector(`[data-id="${id}"]`);
        let notificationTitle = 'cette notification';
        if (card) {
            const titleElement = card.querySelector('h6');
            if (titleElement) {
                notificationTitle = `"${titleElement.textContent.trim()}"`;
            }
        }

        customConfirm(
            `Êtes-vous sûr de vouloir supprimer ${notificationTitle} ?<br><br><small class="text-muted">Cette action est irréversible et la notification sera définitivement supprimée.</small>`,
            function() {
                // Confirmation : supprimer la notification
                fetch(`/admin/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`[data-id="${id}"]`);
                        if (card) {
                            card.style.transition = 'opacity 0.3s';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        }
                        showAlert('Notification supprimée', 'success');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showAlert('Erreur lors de la suppression', 'error');
                });
            },
            null, // Pas de fonction d'annulation
            'Supprimer la notification',
            'Supprimer',
            'Annuler'
        );
    };

    // Marquer toutes comme lues - Fonction masquée mais conservée
    /*
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🔔 Bouton "Tout marquer comme lu" cliqué');
            
            // Désactiver le bouton pendant le traitement
            const originalText = markAllReadBtn.innerHTML;
            markAllReadBtn.disabled = true;
            markAllReadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Traitement...';
            
            // Récupérer le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('❌ Token CSRF non trouvé');
                markAllReadBtn.disabled = false;
                markAllReadBtn.innerHTML = originalText;
                alert('Erreur: Token CSRF manquant. Veuillez rafraîchir la page.');
                return;
            }
            
            const url = '{{ route("admin.notifications.mark-all-read") }}';
            console.log('📡 Envoi de la requête vers:', url);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                credentials: 'same-origin'
            })
            .then(async response => {
                console.log('📥 Réponse reçue:', response.status, response.statusText);
                
                // Vérifier si la réponse est OK
                if (!response.ok) {
                    let errorData;
                    try {
                        errorData = await response.json();
                    } catch (e) {
                        errorData = { message: `Erreur HTTP ${response.status}` };
                    }
                    console.error('❌ Erreur HTTP:', response.status, errorData);
                    throw new Error(errorData.message || `Erreur HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ Données reçues:', data);
                
                if (data.success) {
                    console.log(`✅ ${data.count || 0} notification(s) marquée(s) comme lue(s)`);
                    
                    // Recharger les notifications pour mettre à jour l'affichage
                    loadNotifications(1, false);
                    
                    // Afficher un message de succès
                    if (typeof showAlert === 'function') {
                        showAlert(data.message || 'Toutes les notifications ont été marquées comme lues', 'success');
                    } else {
                        alert(data.message || 'Toutes les notifications ont été marquées comme lues');
                    }
                } else {
                    console.error('❌ Erreur dans la réponse:', data);
                    throw new Error(data.message || 'Erreur lors du marquage des notifications');
                }
            })
            .catch(error => {
                console.error('❌ Erreur détaillée:', error);
                console.error('Stack:', error.stack);
                const errorMessage = error.message || 'Erreur lors du marquage des notifications';
                
                if (typeof showAlert === 'function') {
                    showAlert(errorMessage, 'error');
                } else {
                    alert('Erreur: ' + errorMessage);
                }
            })
            .finally(() => {
                // Réactiver le bouton
                markAllReadBtn.disabled = false;
                markAllReadBtn.innerHTML = originalText;
                console.log('🔔 Bouton réactivé');
            });
        });
        
        console.log('✅ Event listener ajouté au bouton "Tout marquer comme lu"');
    } else {
        console.error('❌ Bouton "Tout marquer comme lu" non trouvé dans le DOM');
    }
    */

    // Actualiser
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            loadNotifications(1, false);
        });
    }

    // Afficher une erreur
    function showError(message) {
        notificationsContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>${message}
            </div>
        `;
    }

    // Charger au démarrage
    console.log('🔔 Chargement initial des notifications');
    loadNotifications(1, false);
    
    console.log('✅ Script de notifications initialisé avec succès');
});
</script>
@endsection

