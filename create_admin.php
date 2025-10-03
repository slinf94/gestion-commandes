<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Vérifier si l'admin existe déjà
$existingAdmin = User::where('email', 'admin@admin.com')->first();

if ($existingAdmin) {
    echo "Un admin avec cet email existe déjà.\n";
    echo "Email: admin@admin.com\n";
    echo "Mot de passe: admin123\n";
    exit;
}

// Créer l'admin
$admin = User::create([
    'nom' => 'Admin',
    'prenom' => 'System',
    'email' => 'admin@admin.com',
    'password' => Hash::make('admin123'),
    'numero_telephone' => '0000000000',
    'ville' => 'Abidjan',
    'role' => 'admin',
    'status' => 'active',
]);

echo "✅ Admin créé avec succès!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📧 Email: admin@admin.com\n";
echo "🔑 Mot de passe: admin123\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🌐 URL Admin: http://127.0.0.1:8000/admin/login\n";
