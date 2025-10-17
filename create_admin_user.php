<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CRÉATION D'UN UTILISATEUR ADMIN ===\n\n";

try {
    // Vérifier si un admin existe déjà
    $existingAdmin = User::where('role', 'admin')->first();

    if ($existingAdmin) {
        echo "✅ Administrateur existant trouvé:\n";
        echo "   Email: {$existingAdmin->email}\n";
        echo "   Nom: {$existingAdmin->full_name}\n";
        echo "   Rôle: {$existingAdmin->role}\n";
        echo "   Créé le: {$existingAdmin->created_at}\n";

        // Proposer de réinitialiser le mot de passe
        echo "\nVoulez-vous réinitialiser le mot de passe ? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) === 'y' || trim($line) === 'Y') {
            $newPassword = 'admin123';
            $existingAdmin->password = Hash::make($newPassword);
            $existingAdmin->save();

            echo "✅ Mot de passe réinitialisé!\n";
            echo "   Nouveau mot de passe: {$newPassword}\n";
        }
    } else {
        echo "❌ Aucun administrateur trouvé.\n";
        echo "Création d'un nouvel administrateur...\n";

        $admin = User::create([
            'full_name' => 'Admin Super',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone' => '+22612345678',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        echo "✅ Administrateur créé avec succès!\n";
        echo "   Email: {$admin->email}\n";
        echo "   Mot de passe: admin123\n";
        echo "   Nom: {$admin->full_name}\n";
    }

    echo "\n📋 INSTRUCTIONS DE CONNEXION:\n";
    echo "1. Allez sur: http://192.168.100.73:8000/login\n";
    echo "2. Utilisez les identifiants ci-dessus\n";
    echo "3. Une fois connecté, allez sur: http://192.168.100.73:8000/admin/orders\n";
    echo "4. Cliquez sur l'œil (👁️) d'une commande pour voir les détails\n";
    echo "5. Les boutons 'Traiter' et 'Annuler' devraient maintenant fonctionner!\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez que la base de données est accessible.\n";
}

echo "\n=== FIN ===\n";
