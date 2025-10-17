<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test de création de commande
$baseUrl = 'http://192.168.100.73:8000/api/v1';

echo "=== TEST DE CRÉATION DE COMMANDE ===\n\n";

// D'abord, se connecter pour obtenir un token
echo "1. Connexion pour obtenir un token...\n";
$loginResponse = file_get_contents($baseUrl . '/auth/login', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode([
            'email' => 'admin@example.com',
            'password' => 'password123'
        ])
    ]
]));

if ($loginResponse === false) {
    echo "   ❌ Erreur de connexion\n";
    exit;
}

$loginData = json_decode($loginResponse, true);
if (!$loginData['success']) {
    echo "   ❌ Échec de la connexion: " . ($loginData['message'] ?? 'Raison inconnue') . "\n";
    exit;
}

$token = $loginData['data']['token'];
echo "   ✅ Connexion réussie, token obtenu\n\n";

// Ajouter des articles au panier
echo "2. Ajout d'articles au panier...\n";
$sessionId = 'test_' . time();

$addToCartData = [
    'product_id' => 16, // HPZBOOOK
    'quantity' => 2
];

$cartContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ],
        'content' => json_encode($addToCartData)
    ]
]);

$cartResponse = file_get_contents($baseUrl . '/cart/add?session_id=' . $sessionId, false, $cartContext);

if ($cartResponse !== false) {
    $cartData = json_decode($cartResponse, true);
    if ($cartData['success']) {
        echo "   ✅ Article ajouté au panier\n";
    } else {
        echo "   ❌ Erreur d'ajout au panier: " . ($cartData['message'] ?? 'Raison inconnue') . "\n";
    }
} else {
    echo "   ❌ Erreur de connexion pour l'ajout au panier\n";
}

// Créer la commande
echo "\n3. Création de la commande...\n";
$orderData = [
    'delivery_address' => [
        'street' => 'Rue de test',
        'city' => 'Ouagadougou',
        'country' => 'Burkina Faso'
    ],
    'delivery_date' => null,
    'delivery_time_slot' => null,
    'notes' => 'Commande de test'
];

$orderContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'X-Session-ID: ' . $sessionId
        ],
        'content' => json_encode($orderData)
    ]
]);

$orderResponse = file_get_contents($baseUrl . '/orders', false, $orderContext);

if ($orderResponse !== false) {
    $orderResult = json_decode($orderResponse, true);
    if ($orderResult['success']) {
        echo "   ✅ Commande créée avec succès!\n";
        echo "   📋 ID de commande: " . $orderResult['data']['id'] . "\n";
        echo "   💰 Montant total: " . $orderResult['data']['total_amount'] . " F\n";

        // Vérifier les articles de la commande
        if (isset($orderResult['data']['items']) && !empty($orderResult['data']['items'])) {
            echo "   📦 Articles dans la commande:\n";
            foreach ($orderResult['data']['items'] as $item) {
                echo "      - " . $item['product_name'] . " (x" . $item['quantity'] . ") - " . $item['total_price'] . " F\n";
            }
        }
    } else {
        echo "   ❌ Erreur de création de commande: " . ($orderResult['message'] ?? 'Raison inconnue') . "\n";
    }
} else {
    echo "   ❌ Erreur de connexion pour la création de commande\n";
}

echo "\n=== FIN DU TEST ===\n";
