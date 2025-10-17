@extends('admin.layouts.app')

@section('title', 'Détails de la Commande')
@section('page-title', 'Détails de la Commande')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Commande #{{ $order->order_number }}</h4>
        <small class="text-muted">Informations détaillées et gestion du statut</small>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informations de la commande -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Informations de la Commande
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Numéro de commande:</strong></td>
                                <td>#{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date de création:</strong></td>
                                <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Statut actuel:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $order->getStatusClass() }} fs-6">
                                        {{ $order->getStatusIcon() }} {{ $order->getStatusLabel() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td><strong class="text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Sous-total:</strong></td>
                                <td>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Taxes:</strong></td>
                                <td>{{ number_format($order->tax_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Remise:</strong></td>
                                <td>{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Frais de livraison:</strong></td>
                                <td>{{ number_format($order->shipping_cost, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations client -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Informations Client
                </h5>
            </div>
            <div class="card-body">
                @if($order->user)
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom complet:</strong></td>
                                    <td>{{ $order->user->nom }} {{ $order->user->prenom }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $order->user->email }}" class="text-decoration-none">
                                            {{ $order->user->email }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Téléphone:</strong></td>
                                    <td>
                                        <a href="tel:{{ $order->user->numero_telephone }}" class="text-decoration-none">
                                            {{ $order->user->numero_telephone }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Localisation:</strong></td>
                                    <td>{{ $order->user->localisation ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut du compte:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $order->user->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($order->user->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Membre depuis:</strong></td>
                                    <td>{{ $order->user->created_at ? $order->user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Utilisateur supprimé - Les informations ne sont plus disponibles
                    </div>
                @endif
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>Articles Commandés
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Image</th>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @if($item->product && $item->product->mainImage)
                                        <img src="{{ $item->product->mainImage }}"
                                             class="img-thumbnail"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             alt="{{ $item->product->name }}"
                                             onerror="this.src='{{ asset('images/placeholder.svg') }}'">
                                    @else
                                        <div class="img-thumbnail d-flex align-items-center justify-content-center bg-light"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $item->product_name ?? ($item->product->name ?? 'Produit supprimé') }}</strong>
                                        @if($item->product_sku)
                                            <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                        @endif
                                        @if($item->product && $item->product->description)
                                            <br><small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->quantity }}</span>
                                </td>
                                <td><strong>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="4">Total de la commande</th>
                                <th class="text-primary">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Gestion du statut -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Gestion du Statut
                </h5>
            </div>
            <div class="card-body">
                <!-- Statut actuel -->
                <div class="alert alert-{{ $order->getStatusClass() }} mb-3">
                    <div class="d-flex align-items-center">
                        <span class="me-2">{{ $order->getStatusIcon() }}</span>
                        <div>
                            <strong>Statut actuel:</strong> {{ $order->getStatusLabel() }}<br>
                            <small>{{ $order->getStatusDescription() }}</small>
                        </div>
                    </div>
                </div>

                <!-- Actions possibles -->
                @if($order->isActive())
                    <h6 class="mb-3">Actions disponibles:</h6>
                    <div class="d-grid gap-2">
                        @foreach($order->getNextPossibleStatuses() as $nextStatus)
                            <button class="btn btn-{{ $nextStatus->getBootstrapClass() }} btn-sm"
                                    onclick="changeOrderStatus('{{ $order->id }}', '{{ $nextStatus->value }}', '{{ $nextStatus->getLabel() }}')"
                                    data-original-text="{{ $nextStatus->getIcon() }} {{ $nextStatus->getLabel() }}">
                                {{ $nextStatus->getIcon() }} {{ $nextStatus->getLabel() }}
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-secondary">
                        <i class="fas fa-info-circle me-2"></i>
                        @if($order->isCancelled())
                            Cette commande est annulée. Aucune action possible.
                        @elseif($order->isCompleted())
                            Cette commande est terminée. Aucune action possible.
                        @endif
                    </div>
                @endif

                <!-- Commentaire pour le changement de statut -->
                <div class="mt-3">
                    <label for="statusComment" class="form-label">Commentaire (optionnel):</label>
                    <textarea class="form-control" id="statusComment" rows="3"
                              placeholder="Ajouter un commentaire pour le changement de statut..."></textarea>
                </div>
            </div>
        </div>

        <!-- Historique des statuts -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Historique des Statuts
                </h5>
            </div>
            <div class="card-body">
                @if($order->statusHistory->count() > 0)
                    <div class="timeline">
                        @foreach($order->statusHistory->sortByDesc('created_at') as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->getNewStatusClass() }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="timeline-title mb-1">
                                            {{ $history->getNewStatusIcon() }} {{ $history->getNewStatusLabel() }}
                                        </h6>
                                        <p class="timeline-text mb-1">
                                            <small class="text-muted">
                                                {{ $history->created_at ? $history->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </small>
                                        </p>
                                        @if($history->comment)
                                            <p class="timeline-text mb-0">
                                                <small>{{ $history->comment }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    <span class="badge badge-{{ $history->getNewStatusClass() }} badge-sm">
                                        {{ $history->getNewStatusLabel() }}
                                    </span>
                                </div>
                                @if($history->changedBy)
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        Par {{ $history->getChangedByName() }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun historique de statut disponible
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px currentColor;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid currentColor;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 12px;
    color: #6c757d;
}

.badge-sm {
    font-size: 0.7em;
}
</style>
@endpush

@section('scripts')
<script>
function changeOrderStatus(orderId, newStatus, statusLabel) {
    const comment = document.getElementById('statusComment').value;

    const message = `Changer le statut de cette commande vers "${statusLabel}" ?`;

    if (confirm(message)) {
        // Désactiver tous les boutons
        const buttons = document.querySelectorAll('button[onclick*="changeOrderStatus"]');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
        });

        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            alert('Erreur: Token CSRF non trouvé. Veuillez recharger la page.');
            return;
        }

        fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: newStatus,
                comment: comment
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Afficher un message de succès
                showAlert('success', data.message);

                // Recharger la page après un court délai
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                throw new Error(data.message || 'Erreur lors de la mise à jour du statut');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            let errorMessage = 'Erreur lors de la mise à jour du statut: ' + error.message;

            // Messages d'erreur spécifiques
            if (error.message.includes('419')) {
                errorMessage = 'Erreur de sécurité (CSRF). Veuillez recharger la page et réessayer.';
            } else if (error.message.includes('401')) {
                errorMessage = 'Session expirée. Veuillez vous reconnecter.';
            } else if (error.message.includes('403')) {
                errorMessage = 'Accès non autorisé.';
            } else if (error.message.includes('422')) {
                errorMessage = 'Changement de statut non autorisé.';
            }

            showAlert('danger', errorMessage);

            // Réactiver les boutons
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = btn.getAttribute('data-original-text') || 'Changer le statut';
            });
        });
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <strong>${type === 'success' ? '✅ Succès!' : '❌ Erreur!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Insérer l'alerte au début du contenu
    const container = document.querySelector('.container-fluid') || document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
}
</script>
@endsection
