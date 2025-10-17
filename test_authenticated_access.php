<?php

// Test d'accès authentifié aux détails de commande
$baseUrl = 'http://192.168.100.73:8000';

echo "=== TEST D'ACCÈS AUTHENTIFIÉ ===\n\n";

// Étape 1: Se connecter
echo "1. Connexion en tant qu'administrateur...\n";

$loginData = [
    'email' => 'admin@admin.com',
    'password' => 'admin123',
    '_token' => '' // On va récupérer le token CSRF
];

// D'abord, récupérer la page de connexion pour obtenir le token CSRF
$loginPageContext = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Test Script'
        ]
    ]
]);

$loginPage = file_get_contents("{$baseUrl}/admin/login", false, $loginPageContext);

if ($loginPage === false) {
    echo "   ❌ Impossible d'accéder à la page de connexion\n";
    exit;
}

// Extraire le token CSRF
if (preg_match('/name="_token" value="([^"]+)"/', $loginPage, $matches)) {
    $csrfToken = $matches[1];
    echo "   ✅ Token CSRF récupéré\n";
} else {
    echo "   ❌ Token CSRF non trouvé\n";
    exit;
}

// Maintenant, se connecter
$loginContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Test Script'
        ],
        'content' => http_build_query([
            'email' => 'admin@admin.com',
            'password' => 'admin123',
            '_token' => $csrfToken
        ])
    ]
]);

$loginResponse = file_get_contents("{$baseUrl}/admin/login", false, $loginContext);

if ($loginResponse === false) {
    echo "   ❌ Échec de la connexion\n";
    exit;
}

// Vérifier si la connexion a réussi (redirection vers dashboard ou admin)
if (strpos($loginResponse, 'dashboard') !== false || strpos($loginResponse, 'admin') !== false) {
    echo "   ✅ Connexion réussie\n";
} else {
    echo "   ⚠️  Connexion possible, mais redirection non détectée\n";
}

echo "\n2. Test d'accès aux détails de commande...\n";

// Maintenant, tester l'accès aux détails de commande
$orderId = 106;

$orderContext = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: Test Script'
        ]
    ]
]);

$orderResponse = file_get_contents("{$baseUrl}/admin/orders/{$orderId}", false, $orderContext);

if ($orderResponse !== false) {
    // Analyser la réponse
    $hasTitle = strpos($orderResponse, "Commande #{$orderId}") !== false;
    $hasButtons = strpos($orderResponse, 'updateOrderStatus') !== false;
    $hasCSRF = strpos($orderResponse, 'csrf-token') !== false;
    $hasLogin = strpos($orderResponse, 'Connexion') !== false;

    echo "   " . ($hasTitle ? '✅' : '❌') . " Titre de commande présent\n";
    echo "   " . ($hasButtons ? '✅' : '❌') . " Fonction JavaScript présente\n";
    echo "   " . ($hasCSRF ? '✅' : '❌') . " Token CSRF présent\n";
    echo "   " . ($hasLogin ? '⚠️' : '✅') . " Pas de redirection login\n";

    if ($hasTitle && $hasButtons && $hasCSRF && !$hasLogin) {
        echo "\n   🎉 SUCCÈS! La page de détails est accessible avec tous les éléments!\n";
        echo "   💡 Les boutons devraient maintenant fonctionner dans le navigateur.\n";
    } else {
        echo "\n   ⚠️  Problème détecté dans la page de détails.\n";
    }
} else {
    echo "   ❌ Impossible d'accéder aux détails de commande\n";
}

echo "\n=== FIN DU TEST ===\n";
