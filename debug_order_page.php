<?php

// Debug de la page de détails de commande
$baseUrl = 'http://192.168.100.73:8000';

echo "=== DEBUG DE LA PAGE DE DÉTAILS DE COMMANDE ===\n\n";

// Test avec différents IDs de commande
$orderIds = [106, 107, 105, 104];

foreach ($orderIds as $orderId) {
    echo "Test pour la commande #{$orderId}:\n";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Debug Script'
            ]
        ]
    ]);

    $response = file_get_contents("{$baseUrl}/admin/orders/{$orderId}", false, $context);

    if ($response !== false) {
        // Analyser le contenu
        $hasTitle = strpos($response, 'Commande #') !== false;
        $hasButtons = strpos($response, 'updateOrderStatus') !== false;
        $hasCSRF = strpos($response, 'csrf-token') !== false;
        $hasLayout = strpos($response, 'admin/layouts/app') !== false;
        $hasLogin = strpos($response, 'login') !== false;
        $hasAuth = strpos($response, 'authenticate') !== false;

        echo "   ✅ Page accessible\n";
        echo "   " . ($hasTitle ? '✅' : '❌') . " Titre de commande\n";
        echo "   " . ($hasButtons ? '✅' : '❌') . " Boutons JavaScript\n";
        echo "   " . ($hasCSRF ? '✅' : '❌') . " Token CSRF\n";
        echo "   " . ($hasLayout ? '✅' : '❌') . " Layout admin\n";
        echo "   " . ($hasLogin ? '⚠️' : '✅') . " Redirection login\n";
        echo "   " . ($hasAuth ? '⚠️' : '✅') . " Problème auth\n";

        // Si pas de boutons, vérifier pourquoi
        if (!$hasButtons) {
            if ($hasLogin || $hasAuth) {
                echo "   🔍 Problème d'authentification détecté\n";
            } else {
                echo "   🔍 Problème de chargement de la vue\n";
            }
        }

        // Extraire un extrait du HTML pour debug
        $htmlSnippet = substr($response, 0, 500);
        echo "   HTML (500 premiers caractères):\n";
        echo "   " . str_replace(["\n", "\r"], [' ', ' '], $htmlSnippet) . "...\n";

    } else {
        echo "   ❌ Page inaccessible\n";
    }

    echo "\n";
}

echo "=== FIN DU DEBUG ===\n";
