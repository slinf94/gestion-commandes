<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "========================================\n";
echo "CREATION DU COMPTE SUPER ADMIN\n";
echo "========================================\n\n";

try {
    // ÉTAPE 1 : Modifier la colonne role
    echo "ETAPE 1 : Ajout du role 'super-admin'...\n";
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire', 'vendeur', 'commercial', 'super-admin') DEFAULT 'client'");
    echo "✓ Role 'super-admin' ajoute !\n\n";

    // ÉTAPE 2 : Créer le super admin
    echo "ETAPE 2 : Creation du compte super admin...\n";
    $superAdmin = User::updateOrCreate(
        ['email' => 'superadmin@allomobile.com'],
        [
            'nom' => 'Super',
            'prenom' => 'Admin',
            'email' => 'superadmin@allomobile.com',
            'password' => Hash::make('SuperAdmin123!'),
            'role' => 'super-admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'numero_telephone' => '+226 70 00 00 01',
            'localisation' => 'Ouagadougou',
            'quartier' => 'Secteur 1',
        ]
    );

    echo "✓ Super admin cree avec succes !\n\n";

    echo "========================================\n";
    echo "INFORMATIONS DE CONNEXION\n";
    echo "========================================\n\n";
    echo "📧 Email       : superadmin@allomobile.com\n";
    echo "🔑 Mot de passe : SuperAdmin123!\n";
    echo "🌐 URL         : http://127.0.0.1:8000/admin/login\n";
    echo "👤 Role        : super-admin\n";
    echo "🆔 ID          : " . $superAdmin->id . "\n\n";

    echo "========================================\n";
    echo "✅ TERMINE !\n";
    echo "========================================\n\n";
    echo "Connectez-vous maintenant sur :\n";
    echo "http://127.0.0.1:8000/admin/login\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    exit(1);
}
