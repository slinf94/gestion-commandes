<?php

/**
 * Script pour CORRIGER les rôles incorrects
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "\n🔧 CORRECTION DES RÔLES INCORRECTS\n";
echo "===================================\n\n";

// Mapping correct des utilisateurs
$usersToFix = [
    ['email' => 'super@admin.com', 'correct_role' => 'super-admin'],
    ['email' => 'admin@test.com', 'correct_role' => 'admin'],
    ['email' => 'gestionnaire@test.com', 'correct_role' => 'gestionnaire'],
    ['email' => 'vendeur@test.com', 'correct_role' => 'vendeur'],
    ['email' => 'admin@monprojet.com', 'correct_role' => 'admin'],
];

foreach ($usersToFix as $userData) {
    $user = User::where('email', $userData['email'])->first();

    if (!$user) {
        echo "❌ Utilisateur non trouvé: {$userData['email']}\n";
        continue;
    }

    echo "\n🔍 Correction de: {$user->email}\n";

    // 1. Retirer TOUS les rôles existants
    $user->roles()->detach();
    echo "   ✓ Anciens rôles RBAC supprimés\n";

    // 2. Attacher le bon rôle RBAC
    $role = Role::where('slug', $userData['correct_role'])->first();
    if ($role) {
        $user->attachRole($role);
        echo "   ✓ Rôle RBAC '{$userData['correct_role']}' attaché\n";
    }

    // 3. Corriger le champ legacy si nécessaire
    $correctLegacyRole = match($userData['correct_role']) {
        'super-admin' => 'admin',
        'admin' => 'admin',
        'gestionnaire' => 'gestionnaire',
        'vendeur' => 'vendeur',
        default => 'client'
    };

    if ($user->role !== $correctLegacyRole) {
        $user->update(['role' => $correctLegacyRole]);
        echo "   ✓ Champ legacy 'role' corrigé: {$correctLegacyRole}\n";
    }
}

echo "\n\n✅ VÉRIFICATION FINALE\n";
echo "=====================\n\n";

use App\Helpers\AdminMenuHelper;

foreach (User::with('roles')->get() as $user) {
    $rbacRoles = AdminMenuHelper::getAllRoles($user);

    echo sprintf("%-40s", $user->nom . " " . $user->prenom);
    echo sprintf("Legacy: %-12s", $user->role ?? 'NULL');
    echo "RBAC: " . (!empty($rbacRoles) ? implode(', ', $rbacRoles) : 'AUCUN');
    echo "\n";
}

echo "\n🎉 Terminé!\n\n";

