<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande reçue</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .alert {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .customer-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #4CAF50;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn:hover {
            background: #2E7D32;
        }
        .urgent {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛒 Allo Mobile</div>
            <h1>Nouvelle commande reçue !</h1>
        </div>

        <div class="alert">
            <div class="alert-icon">🛒</div>
            <h2>Action requise</h2>
            <p>Une nouvelle commande vient d'être passée sur votre plateforme Allo Mobile.</p>
        </div>

        <div class="order-details">
            <h3>📋 Informations de la commande</h3>
            <p><strong>Numéro de commande :</strong> #{{ $order->order_number }}</p>
            <p><strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Statut actuel :</strong> {{ $statusInfo['text'] }}</p>
            <p><strong>Montant total :</strong> <span style="color: #4CAF50; font-weight: bold;">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span></p>
        </div>

        <div class="customer-info">
            <h3>👤 Informations du client</h3>
            <p><strong>Nom complet :</strong> {{ $order->user->full_name }}</p>
            <p><strong>Email :</strong> {{ $order->user->email }}</p>
            <p><strong>Téléphone :</strong> {{ $order->user->numero_telephone }}</p>
            <p><strong>Localisation :</strong> {{ $order->user->localisation }}, {{ $order->user->quartier }}</p>
        </div>

        <div class="order-details">
            <h3>🛍️ Articles commandés</h3>
            @foreach($order->items as $item)
            <div class="order-item">
                <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                <span>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</span>
            </div>
            @endforeach

            <div class="order-item total">
                <span>Total</span>
                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="order-details">
            <h3>📍 Adresse de livraison</h3>
            <p>{{ $order->delivery_address['street'] ?? 'Non spécifiée' }}</p>
            <p>{{ $order->delivery_address['city'] ?? '' }}</p>
            <p>{{ $order->delivery_address['country'] ?? '' }}</p>
        </div>

        @if($order->notes)
        <div class="order-details">
            <h3>📝 Notes du client</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div class="urgent">
            <h3>⚠️ Action requise</h3>
            <p>Veuillez traiter cette commande dans les plus brefs délais pour assurer une expérience client optimale.</p>
        </div>

        <!-- Bouton masqué
        <div style="text-align: center;">
            <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn">Gérer la commande</a>
        </div>
        -->

        <div class="footer">
            <p><strong>Système de notification Allo Mobile</strong></p>
            <p style="font-size: 12px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>


