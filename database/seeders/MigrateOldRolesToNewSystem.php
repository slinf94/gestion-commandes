<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class MigrateOldRolesToNewSystem extends Seeder
{
    /**
     * Migration des anciens rôles (champ 'role') vers le nouveau système RBAC
     *
     * Ce seeder synchronise les rôles de la colonne legacy 'role' avec
     * le système RBAC moderne basé sur les tables roles, permissions, etc.
     */
    public function run(): void
    {
        $this->command->info('🚀 Début de la migration des rôles...');

        // Mapping des anciens rôles vers les nouveaux slugs
        $roleMapping = [
            'admin' => 'admin',
            'gestionnaire' => 'gestionnaire',
            // 'client' => null, // Les clients n'ont pas besoin de rôles RBAC
        ];

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        // Parcourir tous les utilisateurs
        foreach (User::whereNotNull('role')->get() as $user) {
            try {
                // Vérifier si un mapping existe pour ce rôle
                if (!isset($roleMapping[$user->role])) {
                    $this->command->warn("⚠️  Pas de mapping pour le rôle '{$user->role}' (utilisateur: {$user->email})");
                    $skipped++;
                    continue;
                }

                $newRoleSlug = $roleMapping[$user->role];

                // Récupérer le rôle RBAC
                $role = Role::where('slug', $newRoleSlug)->first();

                if (!$role) {
                    $this->command->error("❌ Rôle RBAC '{$newRoleSlug}' introuvable pour {$user->email}");
                    $errors++;
                    continue;
                }

                // Vérifier si l'utilisateur a déjà ce rôle
                if ($user->hasRole($newRoleSlug)) {
                    $this->command->info("✓ {$user->email} a déjà le rôle '{$newRoleSlug}'");
                    $skipped++;
                    continue;
                }

                // Attacher le rôle
                $user->attachRole($role);
                $this->command->info("✅ Rôle '{$newRoleSlug}' attaché à {$user->email}");
                $migrated++;

            } catch (\Exception $e) {
                $this->command->error("❌ Erreur pour {$user->email}: " . $e->getMessage());
                $errors++;
            }
        }

        // Résumé
        $this->command->newLine();
        $this->command->info("📊 Résumé de la migration:");
        $this->command->info("   ✅ Migrés: {$migrated}");
        $this->command->info("   ⏭️  Ignorés: {$skipped}");
        $this->command->info("   ❌ Erreurs: {$errors}");
        $this->command->newLine();

        if ($errors === 0) {
            $this->command->info('🎉 Migration terminée avec succès!');
        } else {
            $this->command->warn("⚠️  Migration terminée avec {$errors} erreur(s)");
        }
    }
}

