<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AddMissingUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'moukoulssoumatao@gmail.com';

        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            echo "✅ Utilisateur trouvé: {$existingUser->email}\n";
            echo "   Nom: {$existingUser->nom} {$existingUser->prenom}\n";
            echo "   Rôle: {$existingUser->role}\n";
            return;
        }

        // Créer l'utilisateur manquant
        echo "❌ Utilisateur non trouvé. Création...\n";

        $user = User::create([
            'nom' => 'Moukouls',
            'prenom' => 'Soumatao',
            'email' => $email,
            'password' => Hash::make('password123'),
            'numero_telephone' => '+22612345678',
            'role' => 'client',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        echo "✅ Utilisateur créé avec succès!\n";
        echo "   Email: {$user->email}\n";
        echo "   Nom: {$user->nom} {$user->prenom}\n";
        echo "   Mot de passe temporaire: password123\n";
    }
}
