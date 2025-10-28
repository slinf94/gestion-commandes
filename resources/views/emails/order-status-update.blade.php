<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de votre commande</title>
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
        .status-change {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .order-details {
            background: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛒 Allo Mobile</div>
            <h1>Mise à jour de votre commande</h1>
        </div>

        <p>Bonjour <strong>{{ $order->user->full_name }}</strong>,</p>

        <p>Nous vous informons que le statut de votre commande a été mis à jour.</p>

        <div class="status-change">
            <div class="status-icon">{{ $newStatusInfo['icon'] }}</div>
            <h2>{{ $newStatusInfo['text'] }}</h2>
            <p>Votre commande est maintenant {{ strtolower($newStatusInfo['text']) }}</p>
        </div>

        <div class="order-details">
            <h3>📋 Détails de la commande</h3>
            <p><strong>Numéro de commande :</strong> #{{ $order->order_number }}</p>
            <p><strong>Date de commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Montant total :</strong> {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>

            <h4>Articles commandés :</h4>
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
            <h3>📝 Notes</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Bouton masqué
        <div style="text-align: center;">
            <a href="{{ url('/orders/' . $order->id) }}" class="btn">Voir ma commande</a>
        </div>
        -->

        <div class="footer">
            <p>Si vous avez des questions concernant votre commande, n'hésitez pas à nous contacter.</p>
            <p><strong>Cordialement,<br>L'équipe Allo Mobile</strong></p>
            <p style="font-size: 12px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
            </p>
        </div>
    </div>
</body>
</html>


