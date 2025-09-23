<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Créer un admin avec un email unique basé sur le timestamp
    $timestamp = time();
    $email = "admin{$timestamp}@example.com";

    $admin = User::create([
        'nom' => 'Administrateur',
        'prenom' => 'Système',
        'email' => $email,
        'password' => Hash::make('admin123'),
        'numero_telephone' => '1234567890',
        'adresse' => 'Adresse Admin',
        'ville' => 'Ville Admin',
        'pays' => 'Pays Admin',
        'code_postal' => '00000',
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    echo "✅ Administrateur créé avec succès !\n";
    echo "ID: " . $admin->id . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Mot de passe: admin123\n";
    echo "Statut: " . $admin->status . "\n";
    echo "\n";
    echo "🔗 CONNEXION ADMIN :\n";
    echo "URL: http://127.0.0.1:8001/admin\n";
    echo "Email: " . $admin->email . "\n";
    echo "Mot de passe: admin123\n";
    echo "\n";
    echo "⚠️  NOTE: Utilisez l'email ci-dessus pour vous connecter\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
