<?php

require_once 'vendor/autoload.php';

// Test de la route de mise à jour du statut
$baseUrl = 'http://192.168.100.73:8000';

echo "=== TEST DE MISE À JOUR DU STATUT DE COMMANDE ===\n\n";

// Simuler une requête POST vers la route de mise à jour du statut
$orderId = 106; // ID de la commande visible dans l'image
$newStatus = 'processing';

$postData = json_encode([
    'status' => $newStatus,
    'comment' => 'Test de mise à jour du statut'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'X-Requested-With: XMLHttpRequest',
            'Accept: application/json',
            'User-Agent: Test Script'
        ],
        'content' => $postData
    ]
]);

echo "1. Test de la route de mise à jour du statut...\n";
echo "   URL: {$baseUrl}/admin/orders/{$orderId}/status\n";
echo "   Méthode: POST\n";
echo "   Données: {$postData}\n\n";

$response = file_get_contents("{$baseUrl}/admin/orders/{$orderId}/status", false, $context);

if ($response !== false) {
    echo "   ✅ Réponse reçue:\n";
    echo "   " . $response . "\n";

    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "   ✅ Format JSON valide\n";
        echo "   ✅ Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        if (isset($data['message'])) {
            echo "   ✅ Message: " . $data['message'] . "\n";
        }
    } else {
        echo "   ⚠️  Réponse non-JSON ou format invalide\n";
    }
} else {
    echo "   ❌ Aucune réponse reçue\n";

    // Vérifier les erreurs HTTP
    $httpCode = 0;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (strpos($header, 'HTTP/') === 0) {
                preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches);
                if (isset($matches[1])) {
                    $httpCode = $matches[1];
                    break;
                }
            }
        }
    }
    echo "   Code HTTP: {$httpCode}\n";
}

echo "\n=== FIN DU TEST ===\n";
