<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "═══════════════════════════════════════════════════════════════\n";
echo "    🔐 CRÉATION DES COMPTES - PROJET SLIMAT\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// ========================================
// 1. CRÉATION DU COMPTE ADMINISTRATEUR
// ========================================

$adminEmail = 'admin@slimat.com';
$existingAdmin = User::where('email', $adminEmail)->first();

if ($existingAdmin) {
    echo "⚠️  Un admin avec cet email existe déjà.\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👤 COMPTE ADMINISTRATEUR (Existant)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email        : $adminEmail\n";
    echo "🔑 Mot de passe : admin123\n";
    echo "👤 Nom complet  : {$existingAdmin->prenom} {$existingAdmin->nom}\n";
    echo "🏷️  Rôle         : {$existingAdmin->role}\n";
    echo "📊 Statut       : {$existingAdmin->status}\n";
    echo "🌐 URL Connexion: http://localhost:8000/admin/login\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
} else {
    $admin = User::create([
        'nom' => 'KOUASSI',
        'prenom' => 'Jean',
        'email' => $adminEmail,
        'password' => Hash::make('admin123'),
        'numero_telephone' => '+225 07 00 00 00 00',
        'numero_whatsapp' => '+225 07 00 00 00 00',
        'localisation' => 'Cocody',
        'quartier' => 'Riviera',
        'role' => 'admin',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    echo "✅ Compte ADMINISTRATEUR créé avec succès!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👤 COMPTE ADMINISTRATEUR (Nouveau)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email        : $adminEmail\n";
    echo "🔑 Mot de passe : admin123\n";
    echo "👤 Nom complet  : Jean KOUASSI\n";
    echo "📱 Téléphone    : +225 07 00 00 00 00\n";
    echo "📍 Localisation : Cocody, Riviera\n";
    echo "🏷️  Rôle         : admin\n";
    echo "📊 Statut       : active\n";
    echo "🌐 URL Connexion: http://localhost:8000/admin/login\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

// ========================================
// 2. CRÉATION DU COMPTE CLIENT
// ========================================

$clientEmail = 'client@slimat.com';
$existingClient = User::where('email', $clientEmail)->first();

if ($existingClient) {
    echo "⚠️  Un client avec cet email existe déjà.\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👤 COMPTE CLIENT (Existant)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email        : $clientEmail\n";
    echo "🔑 Mot de passe : client123\n";
    echo "👤 Nom complet  : {$existingClient->prenom} {$existingClient->nom}\n";
    echo "🏷️  Rôle         : {$existingClient->role}\n";
    echo "📊 Statut       : {$existingClient->status}\n";
    echo "📱 App Mobile   : Utilisez ces identifiants dans l'application\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
} else {
    $client = User::create([
        'nom' => 'DIALLO',
        'prenom' => 'Aminata',
        'email' => $clientEmail,
        'password' => Hash::make('client123'),
        'numero_telephone' => '+225 05 12 34 56 78',
        'numero_whatsapp' => '+225 05 12 34 56 78',
        'localisation' => 'Marcory',
        'quartier' => 'Zone 4',
        'role' => 'client',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    echo "✅ Compte CLIENT créé avec succès!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👤 COMPTE CLIENT (Nouveau)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email        : $clientEmail\n";
    echo "🔑 Mot de passe : client123\n";
    echo "👤 Nom complet  : Aminata DIALLO\n";
    echo "📱 Téléphone    : +225 05 12 34 56 78\n";
    echo "📍 Localisation : Marcory, Zone 4\n";
    echo "🏷️  Rôle         : client\n";
    echo "📊 Statut       : active\n";
    echo "📱 App Mobile   : Utilisez ces identifiants dans l'application\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
}

// ========================================
// 3. RÉSUMÉ FINAL
// ========================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "    📋 RÉSUMÉ DES IDENTIFIANTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "🔐 ADMINISTRATEUR :\n";
echo "   Email    : admin@slimat.com\n";
echo "   Password : admin123\n";
echo "   URL      : http://localhost:8000/admin/login\n\n";

echo "👤 CLIENT :\n";
echo "   Email    : client@slimat.com\n";
echo "   Password : client123\n";
echo "   Utiliser : Application Mobile Flutter\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ Comptes prêts à l'emploi !\n";
echo "═══════════════════════════════════════════════════════════════\n";

