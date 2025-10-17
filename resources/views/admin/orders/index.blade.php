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
                                            @if($order->items->count() > 0)
                                                @foreach($order->items->take(3) as $item)
                                                    @if($item->product && $item->product->mainImage)
                                                        <img src="{{ $item->product->mainImage }}"
                                                             class="img-thumbnail me-1"
                                                             style="width: 30px; height: 30px; object-fit: cover;"
                                                             alt="{{ $item->product->name }}"
                                                             onerror="this.src='{{ asset('images/placeholder.svg') }}'"
                                                             title="{{ $item->product->name }}">
                                                    @else
                                                        <div class="img-thumbnail d-flex align-items-center justify-content-center bg-light me-1"
                                                             style="width: 30px; height: 30px;"
                                                             title="{{ $item->product ? $item->product->name : 'Produit supprimé' }}">
                                                            <i class="fas fa-image text-muted" style="font-size: 10px;"></i>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                @if($order->items->count() > 3)
                                                    <span class="badge bg-secondary ms-1">+{{ $order->items->count() - 3 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Aucun article</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->getStatusClass() }}">
                                            {{ $order->getStatusIcon() }} {{ $order->getStatusLabel() }}
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
