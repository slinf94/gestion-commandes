@extends('admin.layouts.app')

@section('title', 'Détails de la Commande')
@section('page-title', 'Détails de la Commande')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Commande #{{ $order->id }}</h4>
        <small class="text-muted">Informations détaillées et gestion du statut</small>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
    </a>
</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informations de la Commande</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID Commande:</strong></td>
                                            <td>#{{ $order->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'pending' => ['text' => 'En attente', 'class' => 'warning'],
                                                        'confirmed' => ['text' => 'Confirmé', 'class' => 'info'],
                                                        'processing' => ['text' => 'En cours', 'class' => 'info'],
                                                        'shipped' => ['text' => 'Expédié', 'class' => 'info'],
                                                        'delivered' => ['text' => 'Livré', 'class' => 'success'],
                                                        'cancelled' => ['text' => 'Annulé', 'class' => 'danger'],
                                                        'completed' => ['text' => 'Terminé', 'class' => 'success']
                                                    ];
                                                    $status = $statusMap[$order->status] ?? ['text' => ucfirst($order->status), 'class' => 'secondary'];
                                                @endphp
                                                <span class="badge badge-{{ $status['class'] }}">
                                                    {{ $status['text'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td><strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Client</h5>
                                    <table class="table table-borderless">
                                        @if($order->user)
                                            <tr>
                                                <td><strong>Nom:</strong></td>
                                                <td>{{ $order->user->full_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $order->user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Téléphone:</strong></td>
                                                <td>{{ $order->user->numero_telephone }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ville:</strong></td>
                                                <td>{{ $order->user->localisation }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">
                                                    <i class="fas fa-user-slash me-2"></i>
                                                    Utilisateur supprimé
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <h5 class="mt-4">Articles Commandés</h5>
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
                                                @if($item->product->mainImage)
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
                                                    <strong>{{ $item->product->name }}</strong>
                                                    @if($item->product->sku)
                                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                    @endif
                                                    @if($item->product->description)
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
                                        <tr>
                                            <th colspan="4">Total de la commande</th>
                                            <th>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h5>Actions</h5>
                            <div class="d-grid gap-2">
                                @if($order->status == 'pending')
                                    <button class="btn btn-success" onclick="updateOrderStatus('{{ $order->id }}', 'processing')">
                                        <i class="fas fa-play"></i> Traiter la commande
                                    </button>
                                @endif

                                @if($order->status == 'processing')
                                    <button class="btn btn-info" onclick="updateOrderStatus('{{ $order->id }}', 'shipped')">
                                        <i class="fas fa-truck"></i> Marquer comme expédié
                                    </button>
                                @endif

                                @if($order->status == 'shipped')
                                    <button class="btn btn-primary" onclick="updateOrderStatus('{{ $order->id }}', 'completed')">
                                        <i class="fas fa-check"></i> Marquer comme livré
                                    </button>
                                @endif

                                @if($order->status != 'cancelled')
                                    <button class="btn btn-danger" onclick="updateOrderStatus('{{ $order->id }}', 'cancelled')">
                                        <i class="fas fa-times"></i> Annuler la commande
                                    </button>
                                @endif
                            </div>

                            <h5 class="mt-4">Historique des Statuts</h5>
                            <div class="timeline">
                                @foreach($order->statusHistory as $history)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ ucfirst($history->status) }}</h6>
                                        <p class="timeline-text">{{ $history->created_at ? $history->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        @if($history->notes)
                                            <p class="timeline-text">{{ $history->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
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
    background-color: #007bff;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
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
</style>
@endpush

@push('scripts')
<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Êtes-vous sûr de vouloir changer le statut de cette commande ?')) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour du statut');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la mise à jour du statut');
        });
    }
}
</script>
@endpush
@endsection
