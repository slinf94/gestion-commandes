<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "=== CORRECTION DU MOT DE PASSE ADMIN ===\n\n";

        $email = 'admin@admin.com';
        $password = 'admin123';

        // Récupérer l'utilisateur admin
        $user = User::where('email', $email)->first();

        if (!$user) {
            echo "❌ Utilisateur admin non trouvé!\n";
            return;
        }

        echo "✅ Utilisateur trouvé: {$user->email}\n";
        echo "   Rôle: {$user->role}\n";
        echo "   Statut: {$user->status}\n";

        // Forcer la mise à jour du mot de passe
        $user->update([
            'password' => Hash::make($password)
        ]);
        echo "✅ Mot de passe mis à jour: {$password}\n";
        // Vérifier que le mot de passe fonctionne
        $freshUser = $user->fresh();
        $isCorrect = Hash::check($password, $freshUser->password);

        echo "✅ Vérification: " . ($isCorrect ? "MOT DE PASSE CORRECT" : "ERREUR") . "\n";

        echo "\n=== INSTRUCTIONS DE CONNEXION ===\n";
        echo "URL: http://192.168.100.73:8000/admin/login\n";
        echo "Email: {$email}\n";
        echo "Mot de passe: {$password}\n";
        echo "\n=== FIN ===\n";
    }
}
