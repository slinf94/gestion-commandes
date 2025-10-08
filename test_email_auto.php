<?php

/**
 * Test automatique d'envoi d'email (sans confirmation)
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\AccountActivatedNotification;

echo "\n📧 TEST AUTOMATIQUE D'ENVOI D'EMAIL\n";
echo "====================================\n\n";

// Trouver un utilisateur de test
$user = User::where('email', 'superadmin@allomobile.com')->first();

if (!$user) {
    echo "❌ Utilisateur de test non trouvé\n";
    exit(1);
}

echo "🔍 Utilisateur de test :\n";
echo "   - Nom : {$user->prenom} {$user->nom}\n";
echo "   - Email : {$user->email}\n";
echo "   - Statut : {$user->status}\n\n";

// Vérifier la configuration email
echo "🔧 Configuration email :\n";
echo "   - Mailer : " . config('mail.default') . "\n";
echo "   - Host : " . config('mail.mailers.smtp.host') . "\n";
echo "   - Port : " . config('mail.mailers.smtp.port') . "\n";
echo "   - From : " . config('mail.from.address') . "\n";
echo "   - From Name : " . config('mail.from.name') . "\n\n";

echo "📤 Envoi de l'email en cours...\n";

try {
    $user->notify(new AccountActivatedNotification($user));

    echo "✅ Email envoyé avec succès !\n";
    echo "\n📋 Résumé :\n";
    echo "   - Destinataire : {$user->email}\n";
    echo "   - Nom : {$user->prenom} {$user->nom}\n";
    echo "   - Sujet : 🎉 Votre compte Allo Mobile a été activé !\n";
    echo "\n📧 Vérifiez la boîte de réception de : {$user->email}\n";
    echo "   (N'oubliez pas de vérifier le dossier spam)\n\n";

    echo "💡 Conseil : Vérifiez également les logs Laravel :\n";
    echo "   tail -f storage/logs/laravel.log\n\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de l'envoi de l'email !\n";
    echo "   Message : " . $e->getMessage() . "\n";
    echo "   Fichier : " . $e->getFile() . " (ligne " . $e->getLine() . ")\n\n";

    echo "🔧 Vérifications à effectuer :\n";
    echo "   1. Vérifier la configuration dans .env\n";
    echo "   2. Vérifier que le serveur SMTP est accessible\n";
    echo "   3. Vérifier les identifiants Gmail\n";
    echo "   4. Activer 'Accès moins sécurisé' ou utiliser un mot de passe d'application\n";
    echo "   5. Vérifier les logs : storage/logs/laravel.log\n\n";

    exit(1);
}

echo "🎉 Test terminé avec succès !\n";
