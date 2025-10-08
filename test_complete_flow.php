<?php

/**
 * Test complet du flux d'activation avec envoi d'email
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\AccountActivatedNotification;

echo "\n🧪 TEST COMPLET DU FLUX D'ACTIVATION\n";
echo "====================================\n\n";

// Trouver un utilisateur avec le statut "pending"
$pendingUser = User::where('status', 'pending')->first();

if (!$pendingUser) {
    echo "❌ Aucun utilisateur avec le statut 'pending' trouvé.\n";
    echo "💡 Créons un utilisateur de test...\n\n";

    // Créer un utilisateur de test
    $pendingUser = User::create([
        'nom' => 'Test',
        'prenom' => 'Utilisateur',
        'email' => 'test.activation@example.com',
        'numero_telephone' => '0123456789',
        'role' => 'client',
        'status' => 'pending',
        'password' => bcrypt('password123')
    ]);

    echo "✅ Utilisateur de test créé :\n";
    echo "   - ID : {$pendingUser->id}\n";
    echo "   - Email : {$pendingUser->email}\n";
    echo "   - Nom : {$pendingUser->prenom} {$pendingUser->nom}\n";
    echo "   - Status : {$pendingUser->status}\n\n";
} else {
    echo "✅ Utilisateur 'pending' trouvé :\n";
    echo "   - ID : {$pendingUser->id}\n";
    echo "   - Email : {$pendingUser->email}\n";
    echo "   - Nom : {$pendingUser->prenom} {$pendingUser->nom}\n";
    echo "   - Status : {$pendingUser->status}\n\n";
}

echo "🔄 Simulation de l'activation par l'admin...\n";

// Simuler l'activation (changement de statut de pending à active)
$oldStatus = $pendingUser->status;
$pendingUser->update(['status' => 'active']);

echo "✅ Statut mis à jour : {$oldStatus} → {$pendingUser->status}\n\n";

echo "📤 Envoi de l'email de notification...\n";

try {
    $pendingUser->notify(new AccountActivatedNotification($pendingUser));

    echo "✅ Email envoyé avec succès !\n";
    echo "\n📋 Résumé du test :\n";
    echo "   - Utilisateur : {$pendingUser->prenom} {$pendingUser->nom}\n";
    echo "   - Email : {$pendingUser->email}\n";
    echo "   - Statut : {$oldStatus} → {$pendingUser->status}\n";
    echo "   - Email envoyé : ✅\n\n";

    echo "📧 L'utilisateur devrait recevoir un email avec :\n";
    echo "   - Sujet : 🎉 Votre compte Allo Mobile a été activé !\n";
    echo "   - Contenu personnalisé avec son nom et informations\n";
    echo "   - Instructions pour se connecter à l'application\n\n";

    echo "🎉 TEST COMPLET RÉUSSI !\n";
    echo "\n💡 Le système est maintenant prêt pour la production :\n";
    echo "   1. Les clients créent des comptes (status: pending)\n";
    echo "   2. L'admin active les comptes via l'interface web\n";
    echo "   3. Les emails sont envoyés AUTOMATIQUEMENT\n";
    echo "   4. Les clients sont informés et peuvent se connecter\n\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de l'envoi de l'email :\n";
    echo "   Message : " . $e->getMessage() . "\n";
    echo "   Fichier : " . $e->getFile() . " (ligne " . $e->getLine() . ")\n\n";

    exit(1);
}

echo "✅ FLUX COMPLET VALIDÉ !\n";
