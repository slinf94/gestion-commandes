<?php

// Debug spécifique de la page de détails de commande
$baseUrl = 'http://192.168.100.73:8000';

echo "=== DEBUG DÉTAILS DE COMMANDE ===\n\n";

// Test avec un ID de commande existant
$orderId = 106;

echo "Test pour la commande #{$orderId}:\n";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Debug Script',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        ]
    ]
]);

$response = file_get_contents("{$baseUrl}/admin/orders/{$orderId}", false, $context);

if ($response !== false) {
    // Analyser le contenu spécifiquement pour les boutons
    $hasTitle = strpos($response, "Commande #{$orderId}") !== false;
    $hasButtons = strpos($response, 'updateOrderStatus') !== false;
    $hasCSRF = strpos($response, 'csrf-token') !== false;
    $hasLayout = strpos($response, 'admin/layouts/app') !== false;
    $hasLogin = strpos($response, 'Connexion') !== false || strpos($response, 'login') !== false;
    $hasAuth = strpos($response, 'authenticate') !== false;

    echo "   ✅ Page accessible\n";
    echo "   " . ($hasTitle ? '✅' : '❌') . " Titre de commande présent\n";
    echo "   " . ($hasButtons ? '✅' : '❌') . " Fonction updateOrderStatus présente\n";
    echo "   " . ($hasCSRF ? '✅' : '❌') . " Token CSRF présent\n";
    echo "   " . ($hasLayout ? '✅' : '❌') . " Layout admin chargé\n";
    echo "   " . ($hasLogin ? '⚠️' : '✅') . " Redirection login détectée\n";

    // Vérifier les boutons spécifiques
    $hasConfirmButton = strpos($response, 'Confirmer la commande') !== false;
    $hasProcessButton = strpos($response, 'Traiter la commande') !== false;
    $hasCancelButton = strpos($response, 'Annuler la commande') !== false;
    $hasShippedButton = strpos($response, 'Marquer comme expédié') !== false;

    echo "\n   🔍 Boutons spécifiques:\n";
    echo "   " . ($hasConfirmButton ? '✅' : '❌') . " Bouton Confirmer\n";
    echo "   " . ($hasProcessButton ? '✅' : '❌') . " Bouton Traiter\n";
    echo "   " . ($hasCancelButton ? '✅' : '❌') . " Bouton Annuler\n";
    echo "   " . ($hasShippedButton ? '✅' : '❌') . " Bouton Expédier\n";

    // Si pas de boutons, vérifier pourquoi
    if (!$hasButtons) {
        if ($hasLogin || $hasAuth) {
            echo "\n   🔍 PROBLÈME: Redirection vers la page de connexion!\n";
            echo "   💡 SOLUTION: Vous devez vous connecter en tant qu'admin\n";
            echo "   📋 ÉTAPES:\n";
            echo "      1. Allez sur: http://192.168.100.73:8000/login\n";
            echo "      2. Email: admin@admin.com\n";
            echo "      3. Mot de passe: admin123\n";
            echo "      4. Puis retournez sur cette page\n";
        } else {
            echo "\n   🔍 PROBLÈME: Boutons JavaScript manquants\n";
            echo "   💡 SOLUTION: Vérifiez le fichier show.blade.php\n";
        }
    }

    // Extraire un extrait du HTML pour voir le statut
    if (preg_match('/Statut.*?>(.*?)</', $response, $matches)) {
        echo "\n   📊 Statut de la commande: " . trim($matches[1]) . "\n";
    }

    // Vérifier si c'est une page de connexion
    if (strpos($response, 'Connexion Admin') !== false) {
        echo "\n   🚨 ATTENTION: Vous n'êtes pas connecté!\n";
        echo "   Cette page est la page de connexion, pas les détails de commande.\n";
    }

} else {
    echo "   ❌ Page inaccessible\n";
}

echo "\n=== FIN DU DEBUG ===\n";
