<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

/**
 * CreateTestUsersSlimatDraSeeder
 *
 * Crée deux utilisateurs de test : slimat et dra
 */
class CreateTestUsersSlimatDraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== CRÉATION DES UTILISATEURS DE TEST : SLIMAT ET DRA ===');
        $this->command->info('');

        // Créer l'utilisateur "slimat"
        $this->createUser('slimat', [
            'nom' => 'Slimat',
            'prenom' => 'Test',
            'email' => 'slimat@test.com',
            'password' => 'password123',
            'numero_telephone' => '+226 70 11 22 33',
            'role' => 'client',
            'status' => 'active',
        ]);

        // Créer l'utilisateur "dra"
        $this->createUser('dra', [
            'nom' => 'Dra',
            'prenom' => 'Test',
            'email' => 'dra@test.com',
            'password' => 'password123',
            'numero_telephone' => '+226 70 44 55 66',
            'role' => 'client',
            'status' => 'active',
        ]);

        $this->command->info('');
        $this->command->info('=== FIN DE LA CRÉATION DES UTILISATEURS DE TEST ===');
        $this->command->info('');
    }

    /**
     * Créer un utilisateur de test
     */
    private function createUser(string $username, array $data): void
    {
        try {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $data['email'])->first();

            if ($existingUser) {
                $this->command->warn("⚠️  L'utilisateur '{$username}' existe déjà !");
                $this->command->line("   Email: {$existingUser->email}");
                $this->command->line("   Nom: {$existingUser->nom} {$existingUser->prenom}");
                $this->command->line("   Rôle: {$existingUser->role}");
                $this->command->line("   Statut: {$existingUser->status}");
                $this->command->line('');
                return;
            }

            // Créer l'utilisateur
            $this->command->info("🔧 Création de l'utilisateur '{$username}'...");

            $user = User::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'numero_telephone' => $data['numero_telephone'],
                'role' => $data['role'],
                'status' => $data['status'],
                'email_verified_at' => Carbon::now(),
                'localisation' => 'Ouagadougou',
                'quartier' => 'Secteur 30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info("✅ Utilisateur '{$username}' créé avec succès !");
            $this->command->line('');
            $this->displayUserInfo($user, $data['password']);
            $this->command->line('');

        } catch (\Exception $e) {
            $this->command->error("❌ Erreur lors de la création de l'utilisateur '{$username}':");
            $this->command->error("   {$e->getMessage()}");
            $this->command->line('');
        }
    }

    /**
     * Afficher les informations de l'utilisateur créé
     */
    private function displayUserInfo(User $user, string $password): void
    {
        $this->command->line('📋 Informations de connexion :');
        $this->command->line("   👤 Nom complet: {$user->prenom} {$user->nom}");
        $this->command->line("   📧 Email: {$user->email}");
        $this->command->line("   🔑 Mot de passe: {$password}");
        $this->command->line("   📱 Téléphone: {$user->numero_telephone}");
        $this->command->line("   👥 Rôle: {$user->role}");
        $this->command->line("   ✅ Statut: {$user->status}");
        $this->command->line("   🆔 ID: {$user->id}");
    }
}

