<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "========================================\n";
echo "DEBUG CONNEXION SUPER ADMIN\n";
echo "========================================\n\n";

try {
    // ÉTAPE 1 : Vérifier le compte
    echo "ETAPE 1 : Verification du compte...\n";
    $user = User::where('email', 'superadmin@allomobile.com')->first();
    
    if (!$user) {
        echo "✗ PROBLEME : Compte n'existe pas !\n";
        echo "Executez : php force_reset_superadmin.php\n\n";
        exit(1);
    }
    
    echo "✓ Compte existe !\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role (colonne): {$user->role}\n";
    echo "  - Status: {$user->status}\n\n";

    // ÉTAPE 2 : Tester le mot de passe
    echo "ETAPE 2 : Test du mot de passe...\n";
    if (Hash::check('admin123', $user->password)) {
        echo "✓ Mot de passe 'admin123' est valide !\n\n";
    } else {
        echo "✗ Mot de passe 'admin123' est INVALIDE !\n";
        echo "Le mot de passe doit etre reinitialise.\n\n";
    }

    // ÉTAPE 3 : Tester isAdmin()
    echo "ETAPE 3 : Test de la methode isAdmin()...\n";
    if ($user->isAdmin()) {
        echo "✓ isAdmin() retourne TRUE\n\n";
    } else {
        echo "✗ PROBLEME : isAdmin() retourne FALSE !\n";
        echo "La methode isAdmin() ne reconnait pas le super-admin.\n\n";
    }

    // ÉTAPE 4 : Vérifier les rôles dans la table roles
    echo "ETAPE 4 : Verification des roles dans la table 'roles'...\n";
    $rolesCount = $user->roles()->count();
    echo "  - Nombre de roles attaches : {$rolesCount}\n";
    if ($rolesCount > 0) {
        foreach ($user->roles as $role) {
            echo "  - Role : {$role->name} (slug: {$role->slug})\n";
        }
    } else {
        echo "  - Aucun role attache dans la table 'roles'\n";
    }
    echo "\n";

    // ÉTAPE 5 : Correction si nécessaire
    if (!$user->isAdmin()) {
        echo "ETAPE 5 : CORRECTION - Mise a jour du role...\n";
        
        // Forcer le rôle dans la colonne
        DB::table('users')->where('id', $user->id)->update([
            'role' => 'super-admin',
            'status' => 'active'
        ]);
        
        echo "✓ Role mis a jour vers 'super-admin'\n\n";
        
        // Recharger l'utilisateur
        $user = User::find($user->id);
        
        // Tester à nouveau
        echo "Test apres correction...\n";
        if ($user->isAdmin()) {
            echo "✓ isAdmin() retourne maintenant TRUE !\n\n";
        } else {
            echo "✗ PROBLEME PERSISTE : isAdmin() retourne toujours FALSE\n";
            echo "Verification du code User.php necessaire.\n\n";
        }
    }

    echo "========================================\n";
    echo "RESUME\n";
    echo "========================================\n\n";
    echo "Email       : {$user->email}\n";
    echo "Mot de passe : admin123\n";
    echo "Role        : {$user->role}\n";
    echo "isAdmin()   : " . ($user->isAdmin() ? 'OUI' : 'NON') . "\n";
    echo "Status      : {$user->status}\n\n";

    if ($user->isAdmin() && Hash::check('admin123', $user->password)) {
        echo "✅ TOUT EST BON !\n";
        echo "Vous pouvez vous connecter sur : http://127.0.0.1:8000/admin/login\n\n";
        echo "IMPORTANT : Redemarrez le serveur Laravel :\n";
        echo "1. Ctrl+C dans le terminal du serveur\n";
        echo "2. php artisan serve\n\n";
    } else {
        echo "❌ PROBLEMES DETECTES !\n";
        echo "Verifiez les erreurs ci-dessus.\n\n";
    }

} catch (\Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "\nDetails : " . $e->getFile() . " ligne " . $e->getLine() . "\n";
    exit(1);
}
