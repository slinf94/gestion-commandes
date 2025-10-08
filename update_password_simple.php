<?php

/**
 * Mise à jour simple du mot de passe d'application
 */

$envFile = __DIR__ . '/.env';

echo "🔧 MISE À JOUR DU MOT DE PASSE D'APPLICATION\n";
echo "==========================================\n\n";

// Mot de passe d'application fourni par l'utilisateur
$appPassword = "hqat hdgh wrek hilc";

echo "📝 Mot de passe d'application reçu : {$appPassword}\n";
echo "📝 Nom de l'application : Allo Mobile\n\n";

// Lire le fichier .env
$envContent = file_get_contents($envFile);

// Remplacer le mot de passe
$oldPassword = '#294PKm8ssG';
$newPasswordQuoted = '"' . $appPassword . '"';

if (strpos($envContent, $oldPassword) !== false) {
    $envContent = str_replace(
        'MAIL_PASSWORD=' . $oldPassword,
        'MAIL_PASSWORD=' . $newPasswordQuoted,
        $envContent
    );
    
    // Sauvegarder
    file_put_contents($envFile, $envContent);
    
    echo "✅ Mot de passe mis à jour avec succès !\n";
    echo "   Ancien : {$oldPassword}\n";
    echo "   Nouveau : {$appPassword}\n\n";
    
} else {
    echo "❌ Impossible de trouver l'ancien mot de passe dans le fichier .env\n";
    exit(1);
}

echo "🔄 Rechargement de la configuration Laravel...\n";
$output = shell_exec('php artisan config:clear 2>&1');
echo $output . "\n";

echo "✅ Configuration rechargée !\n\n";

echo "🧪 Test de l'envoi d'email...\n";
echo "Exécutez maintenant : php test_email_auto.php\n\n";
