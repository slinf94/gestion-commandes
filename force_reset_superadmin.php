<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "SUPPRESSION FORCEE + CREATION SUPER ADMIN\n";
echo "========================================\n\n";

try {
    // ÉTAPE 1 : Suppression FORCÉE directement dans la base
    echo "ETAPE 1 : Suppression forcee (y compris soft delete)...\n";
    $deleted = DB::table('users')
        ->where('email', 'superadmin@allomobile.com')
        ->delete();
    echo "✓ $deleted compte(s) supprime(s) !\n\n";

    // ÉTAPE 2 : Modifier la colonne role
    echo "ETAPE 2 : Configuration du role 'super-admin'...\n";
    try {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire', 'vendeur', 'commercial', 'super-admin') DEFAULT 'client'");
        echo "✓ Role 'super-admin' configure !\n\n";
    } catch (\Exception $e) {
        echo "- Role deja configure\n\n";
    }

    // ÉTAPE 3 : Créer le nouveau compte DIRECTEMENT avec DB::table
    echo "ETAPE 3 : Creation du nouveau compte...\n";
    $id = DB::table('users')->insertGetId([
        'nom' => 'Super',
        'prenom' => 'Admin',
        'email' => 'superadmin@allomobile.com',
        'password' => Hash::make('admin123'),
        'role' => 'super-admin',
        'status' => 'active',
        'email_verified_at' => now(),
        'numero_telephone' => '+226 70 00 00 01',
        'localisation' => 'Ouagadougou',
        'quartier' => 'Secteur 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✓ Super admin cree avec succes ! (ID: $id)\n\n";

    // ÉTAPE 4 : Vérification
    echo "ETAPE 4 : Verification...\n";
    $check = DB::table('users')
        ->where('email', 'superadmin@allomobile.com')
        ->first();
    
    if ($check) {
        echo "✓ Compte verifie !\n";
        echo "  - ID: " . $check->id . "\n";
        echo "  - Email: " . $check->email . "\n";
        echo "  - Role: " . $check->role . "\n";
        echo "  - Status: " . $check->status . "\n\n";
    }

    // ÉTAPE 5 : Test du mot de passe
    echo "ETAPE 5 : Test du mot de passe...\n";
    if (Hash::check('admin123', $check->password)) {
        echo "✓ Mot de passe valide !\n\n";
    } else {
        echo "✗ Mot de passe invalide !\n\n";
    }

    echo "========================================\n";
    echo "INFORMATIONS DE CONNEXION\n";
    echo "========================================\n\n";
    echo "📧 Email       : superadmin@allomobile.com\n";
    echo "🔑 Mot de passe : admin123\n";
    echo "🌐 URL         : http://127.0.0.1:8000/admin/login\n";
    echo "👤 Role        : super-admin\n";
    echo "🆔 ID          : $id\n\n";

    echo "========================================\n";
    echo "✅ TERMINE !\n";
    echo "========================================\n\n";
    echo "COPIEZ-COLLEZ les identifiants :\n";
    echo "Email : superadmin@allomobile.com\n";
    echo "Mot de passe : admin123\n\n";
    echo "Essayez de vous connecter maintenant !\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "\nDetails : " . $e->getFile() . " ligne " . $e->getLine() . "\n\n";
    
    // En cas d'erreur, afficher les comptes existants
    echo "Comptes avec cet email dans la base :\n";
    $existing = DB::table('users')->where('email', 'superadmin@allomobile.com')->get();
    foreach ($existing as $user) {
        echo "  - ID: {$user->id}, Email: {$user->email}, Deleted: " . ($user->deleted_at ?? 'Non') . "\n";
    }
    
    exit(1);
}
