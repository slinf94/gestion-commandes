<?php

/**
 * Script de test pour l'envoi d'email d'activation
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Notifications\AccountActivatedNotification;

echo "\n📧 TEST D'ENVOI D'EMAIL D'ACTIVATION\n";
echo "=====================================\n\n";

// Demander l'email de l'utilisateur à tester
if ($argc < 2) {
    echo "Usage: php test_activation_email.php <email_utilisateur>\n";
    echo "Exemple: php test_activation_email.php test@example.com\n\n";

    // Afficher quelques utilisateurs disponibles
    echo "📋 Utilisateurs disponibles pour le test :\n";
    $users = User::where('role', 'client')->take(5)->get();
    foreach ($users as $user) {
        echo "  - {$user->email} ({$user->prenom} {$user->nom}) - Statut: {$user->status}\n";
    }
    echo "\n";
    exit(1);
}

$emailToTest = $argv[1];

// Trouver l'utilisateur
echo "🔍 Recherche de l'utilisateur : {$emailToTest}\n";
$user = User::where('email', $emailToTest)->first();

if (!$user) {
    echo "❌ Erreur : Utilisateur non trouvé avec l'email : {$emailToTest}\n";
    exit(1);
}

echo "✅ Utilisateur trouvé !\n";
echo "   - Nom : {$user->prenom} {$user->nom}\n";
echo "   - Email : {$user->email}\n";
echo "   - Téléphone : {$user->telephone}\n";
echo "   - Statut : {$user->status}\n\n";

// Vérifier la configuration email
echo "🔧 Vérification de la configuration email...\n";
$mailConfig = config('mail');
echo "   - Mailer : {$mailConfig['default']}\n";
echo "   - Host : " . config('mail.mailers.smtp.host') . "\n";
echo "   - Port : " . config('mail.mailers.smtp.port') . "\n";
echo "   - From : " . config('mail.from.address') . "\n";
echo "   - From Name : " . config('mail.from.name') . "\n\n";

if ($mailConfig['default'] !== 'smtp') {
    echo "⚠️  Attention : Le mailer par défaut n'est pas SMTP !\n";
    echo "   Modifier le fichier .env : MAIL_MAILER=smtp\n";
    echo "   Puis exécuter : php artisan config:clear\n\n";
}

// Demander confirmation
echo "📧 Êtes-vous prêt à envoyer l'email de test à {$user->email} ? (o/n) : ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'o') {
    echo "❌ Test annulé.\n";
    exit(0);
}

echo "\n📤 Envoi de l'email en cours...\n";

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

