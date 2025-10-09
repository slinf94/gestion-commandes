<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;

// Configuration de base
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de l'endpoint /orders/{id}/cancel ===\n\n";

// Trouver un utilisateur actif
$user = User::where('status', 'active')->first();

if (!$user) {
    echo "❌ Aucun utilisateur actif trouvé.\n";
    exit(1);
}

echo "✅ Utilisateur trouvé: {$user->first_name} {$user->last_name} ({$user->email})\n";

// Trouver une commande en attente
$order = Order::where('status', 'pending')->first();

if (!$order) {
    echo "❌ Aucune commande en attente trouvée.\n";
    exit(1);
}

echo "✅ Commande trouvée: #{$order->id} (Statut: {$order->status})\n";

// Générer un token JWT pour cet utilisateur
try {
    $token = JWTAuth::fromUser($user);
    echo "✅ Token JWT généré: " . substr($token, 0, 20) . "...\n";

    // Tester l'endpoint /orders/{id}/cancel avec curl
    $url = "http://127.0.0.1:8000/api/v1/orders/{$order->id}/cancel";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "✅ URL testée: $url\n";
    echo "✅ Réponse HTTP: $httpCode\n";
    echo "✅ Réponse: $response\n";

    if ($httpCode === 200) {
        echo "🎉 L'endpoint /orders/{id}/cancel fonctionne correctement!\n";

        // Vérifier que le statut de la commande a été mis à jour
        $order->refresh();
        echo "✅ Nouveau statut de la commande: {$order->status}\n";
    } else {
        echo "❌ L'endpoint /orders/{id}/cancel retourne une erreur\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
