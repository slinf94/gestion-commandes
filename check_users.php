<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "📊 VÉRIFICATION DES UTILISATEURS\n";
echo "================================\n\n";

$totalUsers = User::count();
echo "Total d'utilisateurs : {$totalUsers}\n\n";

if ($totalUsers > 0) {
    echo "📋 Premiers utilisateurs :\n";
    $users = User::take(5)->get(['id', 'email', 'prenom', 'nom', 'status']);

    foreach ($users as $user) {
        echo "ID: {$user->id} | Email: {$user->email} | Nom: {$user->prenom} {$user->nom} | Status: {$user->status}\n";
    }

    echo "\n💡 Pour tester l'email, utilisez :\n";
    echo "php test_activation_email.php {$users->first()->email}\n";
} else {
    echo "❌ Aucun utilisateur trouvé dans la base de données.\n";
    echo "💡 Créez d'abord un utilisateur via l'application mobile ou l'interface admin.\n";
}

echo "\n";
