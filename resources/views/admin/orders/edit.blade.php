@extends('admin.layouts.app')

@section('title', 'Modifier la Commande')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier la Commande {{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="status">Statut de la Commande</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="pending" {{ old('status', $order->status->value) == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="processing" {{ old('status', $order->status->value) == 'processing' ? 'selected' : '' }}>En cours de traitement</option>
                                        <option value="shipped" {{ old('status', $order->status->value) == 'shipped' ? 'selected' : '' }}>Expédié</option>
                                        <option value="completed" {{ old('status', $order->status->value) == 'completed' ? 'selected' : '' }}>Livré</option>
                                        <option value="cancelled" {{ old('status', $order->status->value) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="total_amount">Montant Total</label>
                                    <input type="number" step="0.01" class="form-control @error('total_amount') is-invalid @enderror"
                                           id="total_amount" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" required>
                                    @error('total_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="4">{{ old('notes', $order->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h5>Informations Client</h5>
                                <table class="table table-borderless">
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
                                </table>

                                <h5 class="mt-4">Articles Commandés</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produit</th>
                                                <th>Qté</th>
                                                <th>Prix</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






