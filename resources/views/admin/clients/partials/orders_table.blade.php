@forelse($orders as $order)
<tr>
    <td>
        <strong>#{{ $order->order_number }}</strong>
    </td>
    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
    <td>
        @php
            $statusMap = [
                'pending' => ['text' => 'En attente', 'class' => 'warning'],
                'confirmed' => ['text' => 'Confirmé', 'class' => 'info'],
                'processing' => ['text' => 'En cours', 'class' => 'info'],
                'shipped' => ['text' => 'Expédié', 'class' => 'primary'],
                'delivered' => ['text' => 'Livré', 'class' => 'success'],
                'cancelled' => ['text' => 'Annulé', 'class' => 'danger'],
            ];
            $status = $statusMap[$order->status] ?? ['text' => ucfirst($order->status), 'class' => 'secondary'];
        @endphp
        <span class="badge bg-{{ $status['class'] }}">
            {{ $status['text'] }}
        </span>
    </td>
    <td>
        <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
    </td>
    <td>
        <span class="badge bg-light text-dark">
            {{ $order->items->count() }} article(s)
        </span>
    </td>
    <td>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye"></i> Voir
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4">
        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
        <p class="text-muted">Aucune commande trouvée</p>
    </td>
</tr>
@endforelse

