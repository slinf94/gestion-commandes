<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un admin existe déjà
        $existingAdmin = User::where('role', 'admin')->first();

        if ($existingAdmin) {
            echo "✅ Administrateur existant trouvé:\n";
            echo "   Email: {$existingAdmin->email}\n";
            echo "   Nom: {$existingAdmin->nom} {$existingAdmin->prenom}\n";

            // Réinitialiser le mot de passe
            $existingAdmin->password = Hash::make('admin123');
            $existingAdmin->save();

            echo "✅ Mot de passe réinitialisé à: admin123\n";
        } else {
            echo "Création d'un nouvel administrateur...\n";

            $admin = User::create([
                'nom' => 'Admin',
                'prenom' => 'Super',
                'email' => 'admin@allomobile.com',
                'password' => Hash::make('admin123'),
                'numero_telephone' => '+22612345678',
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            echo "✅ Administrateur créé avec succès!\n";
            echo "   Email: {$admin->email}\n";
            echo "   Mot de passe: admin123\n";
            echo "   Nom: {$admin->nom} {$admin->prenom}\n";
        }

        echo "\n📋 INSTRUCTIONS DE CONNEXION:\n";
        echo "1. Allez sur: http://192.168.100.73:8000/login\n";
        echo "2. Utilisez les identifiants ci-dessus\n";
        echo "3. Une fois connecté, allez sur: http://192.168.100.73:8000/admin/orders\n";
        echo "4. Cliquez sur l'œil (👁️) d'une commande pour voir les détails\n";
        echo "5. Les boutons 'Traiter' et 'Annuler' devraient maintenant fonctionner!\n";
    }
}
