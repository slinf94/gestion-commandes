<?php

// Test de vitesse de création de commande
$baseUrl = 'http://192.168.100.73:8000/api/v1';

echo "=== TEST DE VITESSE DE CRÉATION DE COMMANDE ===\n\n";

// Test simple de l'endpoint
echo "1. Test de l'endpoint orders...\n";
$startTime = microtime(true);

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 30
    ]
]);

$response = file_get_contents($baseUrl . '/orders', false, $context);
$endTime = microtime(true);

if ($response !== false) {
    $data = json_decode($response, true);
    $duration = round(($endTime - $startTime) * 1000, 2);
    echo "   ✅ Endpoint accessible en {$duration}ms\n";

    if (isset($data['success']) && $data['success']) {
        echo "   ✅ Format de réponse correct\n";
    } else {
        echo "   ⚠️  Format de réponse: " . ($data['message'] ?? 'Inconnu') . "\n";
    }
} else {
    echo "   ❌ Endpoint inaccessible\n";
}

echo "\n";

// Test de l'endpoint products pour vérifier la vitesse
echo "2. Test de l'endpoint products...\n";
$startTime = microtime(true);

$response = file_get_contents($baseUrl . '/products', false, $context);
$endTime = microtime(true);

if ($response !== false) {
    $data = json_decode($response, true);
    $duration = round(($endTime - $startTime) * 1000, 2);
    echo "   ✅ Endpoint accessible en {$duration}ms\n";

    if (isset($data['data']) && is_array($data['data'])) {
        echo "   ✅ " . count($data['data']) . " produits récupérés\n";
    }
} else {
    echo "   ❌ Endpoint inaccessible\n";
}

echo "\n=== FIN DU TEST ===\n";
