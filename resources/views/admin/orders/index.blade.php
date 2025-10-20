@extends('admin.layouts.app')

@section('title', 'Gestion des Commandes - Allo Mobile Admin')
@section('page-title', 'Gestion des Commandes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Liste des Commandes</h4>
        <small class="text-muted">Suivez et gérez toutes les commandes</small>
    </div>
</div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Articles</th>
                                    <th>Statut</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>
                                        @if($order->user)
                                            {{ $order->user->full_name }}
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash me-1"></i>
                                                Utilisateur supprimé
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                // Version simplifiée pour éviter l'épuisement mémoire
                                                $itemsCount = \DB::table('order_items')->where('order_id', $order->id)->count();
                                            @endphp
                                            @if($itemsCount > 0)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-shopping-cart me-1"></i>
                                                    {{ $itemsCount }} article(s)
                                                </span>
                                            @else
                                                <span class="text-muted">Aucun article</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusClass() }}">
                                            {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucune commande trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

<div class="d-flex justify-content-center mt-4">
    {{ $orders->links() }}
</div>
@endsection
