<?php

// Test simple de la route de mise à jour de commande
$baseUrl = 'http://192.168.100.73:8000';

echo "=== TEST SIMPLE DE MISE À JOUR DE COMMANDE ===\n\n";

// Test 1: Vérifier que la page des détails de commande est accessible
echo "1. Test d'accès à la page des détails de commande...\n";
$orderId = 106;

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Test Script'
        ]
    ]
]);

$response = file_get_contents("{$baseUrl}/admin/orders/{$orderId}", false, $context);

if ($response !== false) {
    // Vérifier si la page contient les éléments attendus
    $containsButtons = strpos($response, 'updateOrderStatus') !== false;
    $containsCSRF = strpos($response, 'csrf-token') !== false;
    $containsOrderId = strpos($response, "Commande #{$orderId}") !== false;

    echo "   ✅ Page accessible\n";
    echo "   " . ($containsButtons ? '✅' : '❌') . " Boutons JavaScript présents\n";
    echo "   " . ($containsCSRF ? '✅' : '❌') . " Token CSRF présent\n";
    echo "   " . ($containsOrderId ? '✅' : '❌') . " ID de commande présent\n";

    if (!$containsCSRF) {
        echo "   ⚠️  PROBLÈME: Token CSRF manquant!\n";
    }
    if (!$containsButtons) {
        echo "   ⚠️  PROBLÈME: Boutons JavaScript manquants!\n";
    }
} else {
    echo "   ❌ Page inaccessible\n";
}

echo "\n2. Test de la route POST (sans authentification)...\n";
$postData = json_encode(['status' => 'confirmed']);

$postContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Test Script'
        ],
        'content' => $postData
    ]
]);

$postResponse = file_get_contents("{$baseUrl}/admin/orders/{$orderId}/status", false, $postContext);

if ($postResponse !== false) {
    echo "   ✅ Route accessible (réponse reçue)\n";
    echo "   Réponse: " . substr($postResponse, 0, 200) . "...\n";
} else {
    echo "   ❌ Route inaccessible ou erreur\n";
}

echo "\n=== FIN DU TEST ===\n";
