<?php

/**
 * Script rapide pour vérifier les rôles de tous les utilisateurs
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Helpers\AdminMenuHelper;

echo "\n🔍 VÉRIFICATION DES RÔLES\n";
echo "==========================\n\n";

foreach (User::with('roles')->get() as $user) {
    $rbacRoles = AdminMenuHelper::getAllRoles($user);

    echo sprintf("Utilisateur: %s %s\n", $user->nom, $user->prenom);
    echo sprintf("  Email: %s\n", $user->email);
    echo sprintf("  Role Legacy: %s\n", $user->role ?? 'NULL');
    echo sprintf("  RBAC Roles: %s\n", !empty($rbacRoles) ? implode(', ', $rbacRoles) : 'AUCUN');
    echo "\n";
}

echo "\n";

