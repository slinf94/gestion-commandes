<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CreateTestClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crée des clients de test pour pouvoir créer des commandes
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('=== CRÉATION DES CLIENTS DE TEST ===');
        $this->command->info('');

        $clients = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@test.com',
                'numero_telephone' => '+237651234567',
                'localisation' => 'Bp 1234 Douala, Akwa',
                'quartier' => 'Akwa',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@test.com',
                'numero_telephone' => '+237652345678',
                'localisation' => 'Rue 125, Bonanjo',
                'quartier' => 'Bonanjo',
            ],
            [
                'nom' => 'Kouam',
                'prenom' => 'Achille',
                'email' => 'achille.kouam@test.com',
                'numero_telephone' => '+237653456789',
                'localisation' => 'Carrefour TKC, Makepe',
                'quartier' => 'Makepe',
            ],
            [
                'nom' => 'Ndi',
                'prenom' => 'Sophie',
                'email' => 'sophie.ndi@test.com',
                'numero_telephone' => '+237654567890',
                'localisation' => 'Avenue Kennedy, New-Bell',
                'quartier' => 'New-Bell',
            ],
            [
                'nom' => 'Tchoupo',
                'prenom' => 'Pierre',
                'email' => 'pierre.tchoupo@test.com',
                'numero_telephone' => '+237655678901',
                'localisation' => 'Quartier Bali, Douala',
                'quartier' => 'Bali',
            ],
            [
                'nom' => 'Nkem',
                'prenom' => 'Claire',
                'email' => 'claire.nkem@test.com',
                'numero_telephone' => '+237656789012',
                'localisation' => 'Deïdo, Douala',
                'quartier' => 'Deïdo',
            ],
            [
                'nom' => 'Ngouo',
                'prenom' => 'Franck',
                'email' => 'franck.ngouo@test.com',
                'numero_telephone' => '+237657890123',
                'localisation' => 'Pk8, Douala',
                'quartier' => 'Pk8',
            ],
            [
                'nom' => 'Mbang',
                'prenom' => 'Emilie',
                'email' => 'emilie.mbang@test.com',
                'numero_telephone' => '+237658901234',
                'localisation' => 'Logpom, Douala',
                'quartier' => 'Logpom',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($clients as $clientData) {
            // Vérifier si le client existe déjà
            $existingClient = User::where('email', $clientData['email'])->first();

            if ($existingClient) {
                $this->command->line("⏭️  Client existant ignoré: {$clientData['email']}");
                $skipped++;
                continue;
            }

            try {
                $client = User::create([
                    'nom' => $clientData['nom'],
                    'prenom' => $clientData['prenom'],
                    'email' => $clientData['email'],
                    'password' => Hash::make('password123'), // Mot de passe par défaut pour les tests
                    'numero_telephone' => $clientData['numero_telephone'],
                    'numero_whatsapp' => $clientData['numero_telephone'],
                    'localisation' => $clientData['localisation'],
                    'quartier' => $clientData['quartier'],
                    'role' => 'client',
                    'status' => 'active',
                    'email_verified_at' => Carbon::now(),
                ]);

                $created++;
                $this->command->info("✅ Client créé: {$client->full_name} ({$client->email})");
            } catch (\Exception $e) {
                $this->command->error("❌ Erreur lors de la création du client {$clientData['email']}: {$e->getMessage()}");
            }
        }

        $this->command->info('');
        $this->command->info("✅ {$created} client(s) créé(s)");
        if ($skipped > 0) {
            $this->command->info("⏭️  {$skipped} client(s) existant(s) ignoré(s)");
        }
        $this->command->info('');
    }
}

