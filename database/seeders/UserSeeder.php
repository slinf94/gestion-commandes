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
 * Ce seeder crée automatiquement les comptes utilisateurs du système :
 * - Un compte administrateur principal
 * - Des comptes commerciaux de test
 * - Des comptes clients de test associés aux commerciaux
 *
 * @author Allo Mobile Team
 * @version 2.0
 * @since Laravel 10+
 */
class UserSeeder extends Seeder
{
    /**
     * Configuration du compte administrateur
     */
    private const ADMIN_EMAIL = 'superadmin@monprojet.com';
    private const ADMIN_PASSWORD = 'SuperAdmin123!';
    private const ADMIN_NOM = 'Super';
    private const ADMIN_PRENOM = 'Admin';
    private const ADMIN_ROLE = 'super-admin';
    private const ADMIN_STATUS = 'active';

    /**
     * Configuration des commerciaux de test
     */
    private const COMMERCIAUX = [
        [
            'email' => 'commercial1@example.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'telephone' => '+226 70 11 11 11',
            'quartier' => 'Secteur 2',
            'nombre_clients' => 3
        ],
        [
            'email' => 'commercial2@example.com',
            'nom' => 'Smith',
            'prenom' => 'Jane',
            'telephone' => '+226 70 22 22 22',
            'quartier' => 'Secteur 3',
            'nombre_clients' => 2
        ]
    ];

    /**
     * Exécute le seeder
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== CRÉATION DES COMPTES UTILISATEURS - PROJET MONPROJET ===');
        $this->command->info('');

        try {
            // Vérifier la connexion à la base de données
            if (!$this->checkDatabaseConnection()) {
                return;
            }

            // Créer l'administrateur principal
            $this->createOrUpdateAdmin();

            // Créer les commerciaux de test
            $this->createCommerciaux();

            // Afficher les statistiques finales
            $this->displayUserStatistics();

        } catch (\Exception $e) {
            $this->command->error('❌ Erreur lors de la création des comptes utilisateurs :');
            $this->command->error($e->getMessage());
            $this->command->error('');
        }

        $this->command->info('=== FIN DE LA CRÉATION DES UTILISATEURS ===');
        $this->command->info('');
    }

    /**
     * Crée ou met à jour le compte administrateur
     *
     * @return void
     */
    private function createOrUpdateAdmin(): void
    {
        $this->command->info('🔧 Traitement du compte administrateur principal...');
        $this->command->info('');

        // Vérifier si l'utilisateur admin existe déjà
        $existingAdmin = User::where('email', self::ADMIN_EMAIL)->first();

        if ($existingAdmin) {
            $this->handleExistingAdmin($existingAdmin);
        } else {
            $this->createNewAdmin();
        }
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
     * Crée les commerciaux de test et leurs clients
     *
     * @return void
     */
    private function createCommerciaux(): void
    {
        $this->command->info('');
        $this->command->info('👥 Création des comptes commerciaux de test...');
        $this->command->info('');

        foreach (self::COMMERCIAUX as $commercialData) {
            $commercial = $this->createCommercial(
                $commercialData['email'],
                $commercialData['nom'],
                $commercialData['prenom'],
                $commercialData['telephone'],
                $commercialData['quartier']
            );

            // Créer les clients pour ce commercial
            $this->createClientsForCommercial($commercial, $commercialData['nombre_clients']);
        }
    }

    /**
     * Crée ou met à jour un compte commercial
     *
     * @param string $email
     * @param string $nom
     * @param string $prenom
     * @param string $telephone
     * @param string $quartier
     * @return User
     */
    private function createCommercial(
        string $email,
        string $nom,
        string $prenom,
        string $telephone,
        string $quartier
    ): User {
        $commercial = User::firstOrCreate(
            ['email' => $email],
            [
                'nom' => $nom,
                'prenom' => $prenom,
                'password' => Hash::make('password'),
                'role' => 'commercial',
                'status' => 'active',
                'email_verified_at' => now(),
                'numero_telephone' => $telephone,
                'localisation' => 'Ouagadougou',
                'quartier' => $quartier,
            ]
        );

        $action = $commercial->wasRecentlyCreated ? 'créé' : 'mis à jour';
        $this->command->info("   ✅ Compte commercial {$prenom} {$nom} {$action}");
        
        return $commercial;
    }

    /**
     * Crée les clients de test pour un commercial
     *
     * @param User $commercial
     * @param int $count
     * @return void
     */
    private function createClientsForCommercial(User $commercial, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $client = User::firstOrCreate(
                ['email' => "client{$i}_com{$commercial->id}@example.com"],
                [
                    'nom' => "Client{$i}",
                    'prenom' => "De {$commercial->prenom}",
                    'password' => Hash::make('password'),
                    'role' => 'client',
                    'status' => 'active',
                    'commercial_id' => $commercial->id,
                    'email_verified_at' => now(),
                    'numero_telephone' => '+226 60 ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'localisation' => 'Ouagadougou',
                    'quartier' => 'Secteur ' . rand(1, 10),
                ]
            );
            
            $action = $client->wasRecentlyCreated ? 'créé' : 'mis à jour';
            $this->command->info("      → Client {$client->prenom} {$client->nom} {$action} pour {$commercial->prenom} {$commercial->nom}");
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
        $this->command->info('│  👤 Nom complet : ' . str_pad($admin->full_name ?? "{$admin->prenom} {$admin->nom}", 35) . ' │');
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
            $this->command->info('│  📧 Email        : ' . str_pad(self::ADMIN_EMAIL, 35) . ' │');
            $this->command->info('│  🔑 Mot de passe : ' . str_pad(self::ADMIN_PASSWORD, 35) . ' │');
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
            $this->command->info('');
            $this->command->info('📊 STATISTIQUES DES UTILISATEURS :');
            $this->command->info('┌─────────────────────────────────────────────────────────┐');
            $this->command->info('│                                                         │');
            
            $totalUsers = User::count();
            $adminUsers = User::where('role', 'admin')->count();
            $commercialUsers = User::where('role', 'commercial')->count();
            $clientUsers = User::where('role', 'client')->count();
            $activeUsers = User::where('status', 'active')->count();

            $this->command->info('│  📈 Total utilisateurs  : ' . str_pad($totalUsers, 29) . ' │');
            $this->command->info('│  👨‍💼 Administrateurs    : ' . str_pad($adminUsers, 29) . ' │');
            $this->command->info('│  💼 Commerciaux         : ' . str_pad($commercialUsers, 29) . ' │');
            $this->command->info('│  👥 Clients             : ' . str_pad($clientUsers, 29) . ' │');
            $this->command->info('│  ✅ Utilisateurs actifs : ' . str_pad($activeUsers, 29) . ' │');
            $this->command->info('│                                                         │');
            $this->command->info('└─────────────────────────────────────────────────────────┘');
            $this->command->info('');
        } catch (\Exception $e) {
            $this->command->warn('⚠️  Impossible de récupérer les statistiques des utilisateurs.');
        }
    }
}