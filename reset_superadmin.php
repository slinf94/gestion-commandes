<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "========================================\n";
echo "REINITIALISATION SUPER ADMIN\n";
echo "========================================\n\n";

try {
    // ÉTAPE 1 : Supprimer l'ancien compte
    echo "ETAPE 1 : Suppression de l'ancien compte...\n";
    User::where('email', 'superadmin@allomobile.com')->delete();
    echo "✓ Ancien compte supprime !\n\n";

    // ÉTAPE 2 : Modifier la colonne role
    echo "ETAPE 2 : Ajout du role 'super-admin'...\n";
    try {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire', 'vendeur', 'commercial', 'super-admin') DEFAULT 'client'");
        echo "✓ Role 'super-admin' ajoute !\n\n";
    } catch (\Exception $e) {
        echo "- Role deja configure\n\n";
    }

    // ÉTAPE 3 : Créer le nouveau compte avec mot de passe simple
    echo "ETAPE 3 : Creation du nouveau compte...\n";
    $superAdmin = User::create([
        'nom' => 'Super',
        'prenom' => 'Admin',
        'email' => 'superadmin@allomobile.com',
        'password' => Hash::make('admin123'),  // Mot de passe simple
        'role' => 'super-admin',
        'status' => 'active',
        'email_verified_at' => now(),
        'numero_telephone' => '+226 70 00 00 01',
        'localisation' => 'Ouagadougou',
        'quartier' => 'Secteur 1',
    ]);

    echo "✓ Super admin cree avec succes !\n\n";

    // ÉTAPE 4 : Vérification
    echo "ETAPE 4 : Verification...\n";
    $check = User::where('email', 'superadmin@allomobile.com')->first();
    if ($check) {
        echo "✓ Compte verifie !\n";
        echo "  - ID: " . $check->id . "\n";
        echo "  - Email: " . $check->email . "\n";
        echo "  - Role: " . $check->role . "\n";
        echo "  - Status: " . $check->status . "\n";
        echo "  - Mot de passe hash: " . substr($check->password, 0, 20) . "...\n\n";
    }

    // ÉTAPE 5 : Test du mot de passe
    echo "ETAPE 5 : Test du mot de passe...\n";
    if (Hash::check('admin123', $check->password)) {
        echo "✓ Mot de passe valide !\n\n";
    } else {
        echo "✗ ERREUR : Mot de passe invalide !\n\n";
    }

    echo "========================================\n";
    echo "INFORMATIONS DE CONNEXION\n";
    echo "========================================\n\n";
    echo "📧 Email       : superadmin@allomobile.com\n";
    echo "🔑 Mot de passe : admin123\n";
    echo "🌐 URL         : http://127.0.0.1:8000/admin/login\n";
    echo "👤 Role        : super-admin\n";
    echo "🆔 ID          : " . $superAdmin->id . "\n\n";

    echo "========================================\n";
    echo "✅ TERMINE !\n";
    echo "========================================\n\n";
    echo "IMPORTANT :\n";
    echo "- Copiez-collez l'email exactement comme indique\n";
    echo "- Le mot de passe est maintenant : admin123\n";
    echo "- Tout en minuscules, pas de caracteres speciaux\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "\nDetails : " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    exit(1);
}
