@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('page-title', 'Mes Notifications')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Mes Notifications</h5>
        <div>
            {{-- Bouton "Tout marquer comme lu" masqué - ne pas supprimer la logique, juste masquer l'UI --}}
            {{-- <button class="btn btn-sm btn-primary" id="markAllReadBtn">
                <i class="fas fa-check-double me-1"></i>Tout marquer comme lu
            </button> --}}
            <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </button>
        </div>
    </div>
    <div class="card-body">
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
            // markAllReadBtn.style.display = 'none'; // Masqué
            return;
        }

        // Vérifier s'il y a des notifications non lues
        const hasUnread = notifications.some(n => !n.is_read);

        notifications.forEach(notification => {
            const card = createNotificationCard(notification);
            notificationsContainer.appendChild(card);
        });

        // Bouton masqué - ne pas supprimer la logique
        // markAllReadBtn.style.display = hasUnread ? 'block' : 'none';
    }

    // Créer une carte de notification
    function createNotificationCard(notification) {
        const card = document.createElement('div');
        card.className = `card notification-card ${notification.is_read ? 'read' : 'unread'}`;
        card.dataset.id = notification.id;
        
        const icon = getNotificationIcon(notification.type);
        const timeAgo = getTimeAgo(notification.created_at);
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <div class="notification-icon">${icon}</div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-1 fw-bold">${escapeHtml(notification.title)}</h6>
                            ${!notification.is_read ? '<span class="badge bg-primary">Nouveau</span>' : ''}
                        </div>
                        <p class="mb-2 text-muted">${escapeHtml(notification.message)}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="notification-time">
                                <i class="fas fa-clock me-1"></i>${timeAgo}
                            </span>
                            <div class="notification-actions">
                                ${!notification.is_read ? `
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(${notification.id})">
                                        <i class="fas fa-check me-1"></i>Marquer lu
                                    </button>
                                ` : ''}
                                <!-- Bouton "Supprimer" masqué - logique conservée dans deleteNotification() -->
                            </div>
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

