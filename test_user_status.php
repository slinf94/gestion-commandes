<?php

require_once 'vendor/autoload.php';

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== TEST DU STATUT DES UTILISATEURS ===\n\n";

// Récupérer tous les utilisateurs
$users = User::all();

echo "Nombre total d'utilisateurs: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "👤 Utilisateur ID: {$user->id}\n";
    echo "   Nom: {$user->nom} {$user->prenom}\n";
    echo "   Email: {$user->email}\n";
    echo "   Statut: {$user->status}\n";
    echo "   Actif: " . ($user->isActive() ? "✅ OUI" : "❌ NON") . "\n";
    echo "   En attente: " . ($user->isPending() ? "⏳ OUI" : "❌ NON") . "\n";
    echo "   Rôle: {$user->role}\n";
    echo "   Créé le: {$user->created_at}\n";
    echo "   Modifié le: {$user->updated_at}\n";
    echo "   " . str_repeat("-", 50) . "\n";
}

echo "\n=== INSTRUCTIONS POUR ACTIVER UN UTILISATEUR ===\n";
echo "Pour activer un utilisateur, utilisez cette commande:\n";
echo "php artisan tinker\n";
echo "App\\Models\\User::find(ID_UTILISATEUR)->update(['status' => 'active']);\n\n";

echo "=== TEST TERMINÉ ===\n";

