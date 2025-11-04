@forelse($orders as $order)
<tr>
    <td>
        <strong>{{ $order->order_number }}</strong>
    </td>
    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
    <td>
        <span class="badge bg-{{ $order->getStatusClass() }}">
            {{ $order->getStatusLabel() }}
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


