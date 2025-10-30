<?php

/**
 * Script rapide pour assigner les bons rôles aux utilisateurs existants
 *
 * Usage: php assign_roles_fix.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "🔧 ASSIGNATION DES RÔLES AUX UTILISATEURS\n";
echo "==========================================\n\n";

// Mapping des anciens rôles vers les nouveaux slugs RBAC
$roleMapping = [
    'admin' => 'admin',
    'gestionnaire' => 'gestionnaire',
    'vendeur' => 'vendeur',
    // 'client' => null, // Les clients n'ont pas besoin de rôles RBAC
];

$migrated = 0;
$skipped = 0;
$errors = 0;

// Parcourir tous les utilisateurs qui ont un rôle legacy
foreach (User::whereNotNull('role')->get() as $user) {
    try {
        // Vérifier si un mapping existe pour ce rôle
        if (!isset($roleMapping[$user->role])) {
            echo "⏭️  Ignoré: {$user->email} (rôle: {$user->role})\n";
            $skipped++;
            continue;
        }

        $newRoleSlug = $roleMapping[$user->role];

        // Récupérer le rôle RBAC
        $role = Role::where('slug', $newRoleSlug)->first();

        if (!$role) {
            echo "❌ Rôle RBAC '{$newRoleSlug}' introuvable pour {$user->email}\n";
            $errors++;
            continue;
        }

        // Vérifier si l'utilisateur a déjà ce rôle
        if ($user->hasRole($newRoleSlug)) {
            echo "✓ {$user->email} a déjà le rôle '{$newRoleSlug}'\n";
            $skipped++;
            continue;
        }

        // Attacher le rôle
        $user->attachRole($role);
        echo "✅ Rôle '{$newRoleSlug}' attaché à {$user->email}\n";
        $migrated++;

    } catch (Exception $e) {
        echo "❌ Erreur pour {$user->email}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Résumé
echo "\n📊 RÉSUMÉ:\n";
echo "   ✅ Migrés: {$migrated}\n";
echo "   ⏭️  Ignorés: {$skipped}\n";
echo "   ❌ Erreurs: {$errors}\n\n";

if ($errors === 0) {
    echo "🎉 Migration terminée avec succès!\n";
} else {
    echo "⚠️  Migration terminée avec {$errors} erreur(s)\n";
}

// Afficher tous les utilisateurs et leurs rôles
echo "\n📋 UTILISATEURS ET LEURS RÔLES:\n";
echo "================================\n";
use App\Helpers\AdminMenuHelper;

foreach (User::with('roles')->get() as $user) {
    $roles = AdminMenuHelper::getAllRoles($user);
    $rolesStr = !empty($roles) ? implode(', ', $roles) : 'Aucun';
    echo sprintf("%-40s -> %s\n", $user->email, $rolesStr);
}

echo "\n";

