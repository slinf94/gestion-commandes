<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

/**
 * UserSeeder
 *
 * Ce seeder crée automatiquement un compte administrateur principal
 * lors de l'exécution de php artisan db:seed.
 *
 * @author Allo Mobile Team
 * @version 1.0
 * @since Laravel 10+
 */
class UserSeeder extends Seeder
{
    /**
     * Configuration du compte administrateur
     */
    private const ADMIN_EMAIL = 'admin@monprojet.com';
    private const ADMIN_PASSWORD = 'admin123';
    private const ADMIN_NOM = 'Admin';
    private const ADMIN_PRENOM = 'Principal';
    private const ADMIN_ROLE = 'admin';
    private const ADMIN_STATUS = 'active';

    /**
     * Exécute le seeder
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== CRÉATION DES COMPTES ADMIN - PROJET MONPROJET ===');
        $this->command->info('');

        try {
            // Vérifier si l'utilisateur admin existe déjà
            $existingAdmin = User::where('email', self::ADMIN_EMAIL)->first();

            if ($existingAdmin) {
                $this->handleExistingAdmin($existingAdmin);
            } else {
                $this->createNewAdmin();
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Erreur lors de la création du compte administrateur :');
            $this->command->error($e->getMessage());
            $this->command->error('');
        }

        $this->command->info('=== FIN DE LA CRÉATION DES COMPTES ADMIN ===');
        $this->command->info('');
    }

    /**
     * Gère le cas où l'administrateur existe déjà
     *
     * @param User $admin
     * @return void
     */
    private function handleExistingAdmin(User $admin): void
    {
        $this->command->warn('⚠️  ATTENTION : Un compte administrateur existe déjà !');
        $this->command->warn('');

        $this->displayAdminInfo($admin, 'EXISTANT');

        $this->command->warn('');
        $this->command->warn('💡 Conseil : Si vous souhaitez recréer le compte, supprimez-le d\'abord de la base de données.');
        $this->command->warn('');
    }

    /**
     * Crée un nouvel administrateur
     *
     * @return void
     */
    private function createNewAdmin(): void
    {
        $this->command->info('🔧 Création du compte administrateur principal...');

        // Démarrer une transaction pour assurer l'intégrité des données
        DB::beginTransaction();

        try {
            $admin = User::create([
                'nom' => self::ADMIN_NOM,
                'prenom' => self::ADMIN_PRENOM,
                'email' => self::ADMIN_EMAIL,
                'password' => Hash::make(self::ADMIN_PASSWORD),
                'role' => self::ADMIN_ROLE,
                'status' => self::ADMIN_STATUS,
                'email_verified_at' => Carbon::now(),
                'numero_telephone' => '+226 70 00 00 00',
                'localisation' => 'Ouagadougou',
                'quartier' => 'Secteur 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Valider la transaction
            DB::commit();

            $this->command->info('✅ Compte administrateur créé avec succès !');
            $this->command->info('');

            $this->displayAdminInfo($admin, 'NOUVEAU');

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Affiche les informations de l'administrateur
     *
     * @param User $admin
     * @param string $type
     * @return void
     */
    private function displayAdminInfo(User $admin, string $type): void
    {
        $this->command->info("📋 INFORMATIONS DU COMPTE ADMINISTRATEUR ({$type}) :");
        $this->command->info('┌─────────────────────────────────────────────────────────┐');
        $this->command->info('│                                                         │');
        $this->command->info('│  👤 Nom complet : ' . str_pad($admin->full_name, 35) . ' │');
        $this->command->info('│  📧 Email       : ' . str_pad($admin->email, 35) . ' │');
        $this->command->info('│  🔑 Rôle        : ' . str_pad(ucfirst($admin->role), 35) . ' │');
        $this->command->info('│  📊 Statut      : ' . str_pad(ucfirst($admin->status), 35) . ' │');
        $this->command->info('│  📅 Créé le     : ' . str_pad($admin->created_at->format('d/m/Y H:i'), 35) . ' │');
        $this->command->info('│  🆔 ID          : ' . str_pad($admin->id, 35) . ' │');
        $this->command->info('│                                                         │');
        $this->command->info('└─────────────────────────────────────────────────────────┘');

        if ($type === 'NOUVEAU') {
            $this->command->info('');
            $this->command->info('🔐 INFORMATIONS DE CONNEXION :');
            $this->command->info('┌─────────────────────────────────────────────────────────┐');
            $this->command->info('│                                                         │');
            $this->command->info('│  📧 Email    : ' . str_pad(self::ADMIN_EMAIL, 35) . ' │');
            $this->command->info('│  🔑 Mot de passe : ' . str_pad(self::ADMIN_PASSWORD, 30) . ' │');
            $this->command->info('│                                                         │');
            $this->command->info('└─────────────────────────────────────────────────────────┘');
            $this->command->info('');
            $this->command->info('🌐 URL de connexion : ' . url('/admin/login'));
            $this->command->info('');
            $this->command->warn('⚠️  IMPORTANT : Changez le mot de passe après la première connexion !');
        } else {
            $this->command->info('');
            $this->command->info('🌐 URL de connexion : ' . url('/admin/login'));
        }
    }

    /**
     * Vérifie la configuration de la base de données
     *
     * @return bool
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->command->error('❌ Impossible de se connecter à la base de données :');
            $this->command->error($e->getMessage());
            return false;
        }
    }

    /**
     * Affiche les statistiques des utilisateurs
     *
     * @return void
     */
    private function displayUserStatistics(): void
    {
        try {
            $totalUsers = User::count();
            $adminUsers = User::where('role', 'admin')->count();
            $activeUsers = User::where('status', 'active')->count();

            $this->command->info('📊 STATISTIQUES DES UTILISATEURS :');
            $this->command->info("   • Total d'utilisateurs : {$totalUsers}");
            $this->command->info("   • Administrateurs : {$adminUsers}");
            $this->command->info("   • Utilisateurs actifs : {$activeUsers}");
            $this->command->info('');
        } catch (\Exception $e) {
            $this->command->warn('⚠️  Impossible de récupérer les statistiques des utilisateurs.');
        }
    }
}

