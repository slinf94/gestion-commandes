<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

// Configuration de base
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de l'endpoint /auth/me ===\n\n";

// Trouver un utilisateur actif
$user = User::where('status', 'active')->first();

if (!$user) {
    echo "❌ Aucun utilisateur actif trouvé. Créons un utilisateur de test...\n";

    $user = User::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'phone' => '1234567890',
        'status' => 'active',
        'role' => 'client'
    ]);

    echo "✅ Utilisateur de test créé: {$user->email}\n";
}

echo "✅ Utilisateur trouvé: {$user->first_name} {$user->last_name} ({$user->email})\n";
echo "✅ Statut: {$user->status}\n";

// Générer un token JWT pour cet utilisateur
try {
    $token = JWTAuth::fromUser($user);
    echo "✅ Token JWT généré: " . substr($token, 0, 20) . "...\n";

    // Tester l'endpoint /auth/me avec curl
    $url = 'http://127.0.0.1:8000/api/v1/auth/me';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "✅ Réponse HTTP: $httpCode\n";
    echo "✅ Réponse: $response\n";

    if ($httpCode === 200) {
        echo "🎉 L'endpoint /auth/me fonctionne correctement!\n";
    } else {
        echo "❌ L'endpoint /auth/me retourne une erreur\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
