<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST EMAIL SMTP GMAIL - ALLO MOBILE ===\n\n";

// Vérifier la configuration
echo "1. Vérification de la configuration...\n";
$config = config('mail');

echo "   Mailer par défaut: " . $config['default'] . "\n";
echo "   Host SMTP: " . $config['mailers']['smtp']['host'] . "\n";
echo "   Port: " . $config['mailers']['smtp']['port'] . "\n";
echo "   Username: " . $config['mailers']['smtp']['username'] . "\n";
echo "   Encryption: " . $config['mailers']['smtp']['encryption'] . "\n";
echo "   From Address: " . $config['from']['address'] . "\n";
echo "   From Name: " . $config['from']['name'] . "\n";

echo "\n";

// Test d'envoi d'email simple
echo "2. Test d'envoi d'email simple...\n";
try {
    $testEmail = 'noreply.allomobile@gmail.com'; // Envoyer à la même adresse pour test
    
    Mail::raw('Test d\'envoi d\'email depuis Allo Mobile avec Gmail SMTP\n\nCeci est un email de test pour vérifier que la configuration fonctionne correctement.', function (Message $message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('✅ Test Email SMTP - Allo Mobile')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "✅ Email de test envoyé avec succès !\n";
    echo "   Vérifiez votre boîte email: {$testEmail}\n";
    echo "   Si l'email n'arrive pas, vérifiez:\n";
    echo "   - Que le mot de passe d'application Gmail est correct\n";
    echo "   - Que l'authentification à 2 facteurs est activée sur le compte Gmail\n";
    echo "   - Que vous avez créé un mot de passe d'application (pas le mot de passe du compte)\n";
    
} catch (Exception $e) {
    echo "❌ Erreur envoi email: " . $e->getMessage() . "\n";
    echo "   Détails: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n";
    echo "   VÉRIFICATIONS IMPORTANTES:\n";
    echo "   1. Le mot de passe doit être un 'Mot de passe d'application' Gmail\n";
    echo "   2. Pour créer un mot de passe d'application:\n";
    echo "      - Allez sur https://myaccount.google.com/security\n";
    echo "      - Activez l'authentification à 2 facteurs\n";
    echo "      - Créez un mot de passe d'application\n";
    echo "      - Utilisez ce mot de passe (format: xxxx xxxx xxxx xxxx)\n";
    echo "   3. Vérifiez que le compte Gmail autorise les applications moins sécurisées\n";
    echo "      (si nécessaire, activez cette option temporairement)\n";
}

echo "\n";
echo "=== FIN DU TEST ===\n";
echo "Si l'email est reçu, la configuration est correcte ! ✅\n";
echo "Si l'email n'est pas reçu, vérifiez les points ci-dessus. ❌\n\n";
















