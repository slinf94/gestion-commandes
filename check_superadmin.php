<?php

/**
 * Script de vérification et création du SuperAdmin
 * Exécuter depuis le dossier gestion-commandes avec: php check_superadmin.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=================================================\n";
echo "   VERIFICATION DU COMPTE SUPERADMIN\n";
echo "=================================================\n\n";

// Vérifier la connexion à la base de données
try {
    DB::connection()->getPdo();
    echo "[OK] Connexion à la base de données réussie\n";
    echo "    Base: " . env('DB_DATABASE') . "\n\n";
} catch (\Exception $e) {
    echo "[ERREUR] Impossible de se connecter à la base de données:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

// Rechercher le superadmin
$email = 'superadmin@monprojet.com';
$superadmin = User::where('email', $email)->first();

if ($superadmin) {
    echo "[TROUVE] Le compte SuperAdmin existe!\n\n";
    echo "Informations du compte:\n";
    echo "------------------------\n";
    echo "ID:       " . $superadmin->id . "\n";
    echo "Nom:      " . $superadmin->nom . " " . $superadmin->prenom . "\n";
    echo "Email:    " . $superadmin->email . "\n";
    echo "Role:     " . $superadmin->role . "\n";
    echo "Status:   " . $superadmin->status . "\n";
    echo "Créé le:  " . $superadmin->created_at . "\n";
    echo "\n";

    // Vérifier si le mot de passe fonctionne
    $testPassword = 'SuperAdmin123!';
    if (Hash::check($testPassword, $superadmin->password)) {
        echo "[OK] Le mot de passe 'SuperAdmin123!' est CORRECT\n";
    } else {
        echo "[ATTENTION] Le mot de passe 'SuperAdmin123!' ne fonctionne PAS\n";
        echo "\nVoulez-vous réinitialiser le mot de passe? (o/n): ";

        // Pour un script automatique, on réinitialise directement
        echo "Réinitialisation automatique...\n";
        $superadmin->password = Hash::make($testPassword);
        $superadmin->save();
        echo "[OK] Mot de passe réinitialisé à 'SuperAdmin123!'\n";
    }

    // Vérifier le rôle
    if ($superadmin->role !== 'super-admin') {
        echo "[ATTENTION] Le rôle n'est pas 'super-admin', correction...\n";
        $superadmin->role = 'super-admin';
        $superadmin->save();
        echo "[OK] Rôle corrigé à 'super-admin'\n";
    }

    // Vérifier le statut
    if ($superadmin->status !== 'active') {
        echo "[ATTENTION] Le statut n'est pas 'active', correction...\n";
        $superadmin->status = 'active';
        $superadmin->save();
        echo "[OK] Statut corrigé à 'active'\n";
    }

} else {
    echo "[NON TROUVE] Le compte SuperAdmin n'existe pas!\n";
    echo "\nCréation du compte...\n";

    try {
        $superadmin = User::create([
            'nom' => 'Super',
            'prenom' => 'Admin',
            'email' => $email,
            'password' => Hash::make('SuperAdmin123!'),
            'role' => 'super-admin',
            'status' => 'active',
            'email_verified_at' => now(),
            'numero_telephone' => '+226 70 00 00 00',
            'localisation' => 'Ouagadougou',
            'quartier' => 'Secteur 1',
        ]);

        echo "[OK] Compte SuperAdmin créé avec succès!\n";
        echo "\nID du nouveau compte: " . $superadmin->id . "\n";
    } catch (\Exception $e) {
        echo "[ERREUR] Impossible de créer le compte:\n";
        echo $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";
echo "=================================================\n";
echo "   IDENTIFIANTS DE CONNEXION\n";
echo "=================================================\n";
echo "\n";
echo "   Email:        superadmin@monprojet.com\n";
echo "   Mot de passe: SuperAdmin123!\n";
echo "\n";
echo "   URL: http://localhost:8000/admin/login\n";
echo "        ou http://192.168.100.73:8000/admin/login\n";
echo "\n";
echo "=================================================\n";
echo "\n";
