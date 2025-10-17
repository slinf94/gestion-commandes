<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== CORRECTION DE L'UTILISATEUR ADMIN ===\n\n";

        $email = 'admin@admin.com';
        $password = 'admin123';

        // Vérifier si l'utilisateur existe
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            echo "✅ Utilisateur admin trouvé: {$existingUser->email}\n";
            echo "   Nom: {$existingUser->nom} {$existingUser->prenom}\n";
            echo "   Rôle: {$existingUser->role}\n";
            echo "   Statut: {$existingUser->status}\n";

            // Mettre à jour le mot de passe pour être sûr
            $existingUser->update([
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'active',
            ]);

            echo "✅ Mot de passe mis à jour: {$password}\n";
            echo "✅ Rôle confirmé: admin\n";
            echo "✅ Statut confirmé: active\n";

        } else {
            echo "❌ Utilisateur admin non trouvé. Création...\n";

            $user = User::create([
                'nom' => 'Admin',
                'prenom' => 'Super',
                'email' => $email,
                'password' => Hash::make($password),
                'numero_telephone' => '+22687654321',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            echo "✅ Utilisateur admin créé avec succès!\n";
            echo "   Email: {$user->email}\n";
            echo "   Nom: {$user->nom} {$user->prenom}\n";
            echo "   Mot de passe: {$password}\n";
        }

        // Vérifier tous les utilisateurs admin
        echo "\n=== TOUS LES UTILISATEURS ADMIN ===\n";
        $adminUsers = User::where('role', 'admin')->get();

        foreach ($adminUsers as $admin) {
            echo "   - {$admin->email} ({$admin->nom} {$admin->prenom}) [{$admin->status}]\n";
        }

        echo "\n=== INSTRUCTIONS DE CONNEXION ===\n";
        echo "URL: http://192.168.100.73:8000/admin/login\n";
        echo "Email: {$email}\n";
        echo "Mot de passe: {$password}\n";
        echo "\n=== FIN ===\n";
    }
}
