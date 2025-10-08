<?php

/**
 * Script de configuration des paramètres d'email pour Gmail
 */

$envFile = __DIR__ . '/.env';

// Lire le fichier .env s'il existe
$envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

// Paramètres email à configurer
$mailConfig = [
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.gmail.com',
    'MAIL_PORT' => '587',
    'MAIL_USERNAME' => 'alnoreply48@gmail.com',
    'MAIL_PASSWORD' => '#294PKm8ssG',
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_FROM_ADDRESS' => 'alnoreply48@gmail.com',
    'MAIL_FROM_NAME' => '"Allo Mobile"',
];

// Fonction pour mettre à jour ou ajouter une variable d'environnement
function updateOrAddEnvVar(&$content, $key, $value) {
    $pattern = "/^{$key}=.*/m";
    $replacement = "{$key}={$value}";

    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        echo "✅ Mise à jour de {$key}\n";
    } else {
        $content .= "\n{$replacement}";
        echo "➕ Ajout de {$key}\n";
    }
}

echo "🔧 Configuration des paramètres d'email...\n\n";

// Mettre à jour ou ajouter chaque paramètre
foreach ($mailConfig as $key => $value) {
    updateOrAddEnvVar($envContent, $key, $value);
}

// Sauvegarder le fichier .env
file_put_contents($envFile, $envContent);

echo "\n✅ Configuration terminée !\n";
echo "\n📝 Paramètres configurés :\n";
echo "   - Serveur SMTP : smtp.gmail.com\n";
echo "   - Port : 587\n";
echo "   - Encryption : TLS\n";
echo "   - Email : alnoreply48@gmail.com\n";
echo "   - Nom : Allo Mobile\n";

echo "\n⚠️  IMPORTANT : Vérifiez les paramètres de sécurité Gmail\n";
echo "   1. Activez l'authentification à 2 facteurs\n";
echo "   2. Générez un mot de passe d'application\n";
echo "   3. Ou activez 'Accès moins sécurisé' (non recommandé)\n";

echo "\n🔄 Exécutez : php artisan config:clear\n";
echo "   pour recharger la configuration.\n\n";

