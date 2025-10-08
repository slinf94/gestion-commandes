<?php

/**
 * Script pour mettre à jour le mot de passe email dans .env
 */

$envFile = __DIR__ . '/.env';

echo "🔧 MISE À JOUR DU MOT DE PASSE EMAIL\n";
echo "====================================\n\n";

echo "⚠️  IMPORTANT : Vous devez d'abord avoir généré un mot de passe d'application sur Gmail.\n\n";

echo "📝 Étapes à suivre :\n";
echo "1. Allez sur : https://myaccount.google.com/apppasswords\n";
echo "2. Sélectionnez 'Mail' et 'Autre (nom personnalisé)'\n";
echo "3. Tapez : 'Allo Mobile Backend'\n";
echo "4. Cliquez sur 'Générer'\n";
echo "5. COPIEZ le mot de passe de 16 caractères (ex: abcd efgh ijkl mnop)\n\n";

echo "Entrez le mot de passe d'application (16 caractères) : ";
$handle = fopen("php://stdin", "r");
$newPassword = trim(fgets($handle));
fclose($handle);

if (empty($newPassword)) {
    echo "❌ Mot de passe vide. Opération annulée.\n";
    exit(1);
}

if (strlen(str_replace(' ', '', $newPassword)) !== 16) {
    echo "⚠️  Attention : Le mot de passe d'application doit faire exactement 16 caractères.\n";
    echo "   Longueur actuelle : " . strlen(str_replace(' ', '', $newPassword)) . " caractères\n";
    echo "   Continuer quand même ? (o/n) : ";
    
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirm) !== 'o') {
        echo "❌ Opération annulée.\n";
        exit(1);
    }
}

// Lire le fichier .env
$envContent = file_get_contents($envFile);

// Remplacer le mot de passe
$oldPassword = '#294PKm8ssG';
$newPasswordQuoted = '"' . $newPassword . '"';

if (strpos($envContent, $oldPassword) !== false) {
    $envContent = str_replace(
        'MAIL_PASSWORD=' . $oldPassword,
        'MAIL_PASSWORD=' . $newPasswordQuoted,
        $envContent
    );
    
    // Sauvegarder
    file_put_contents($envFile, $envContent);
    
    echo "\n✅ Mot de passe mis à jour avec succès !\n";
    echo "   Ancien : {$oldPassword}\n";
    echo "   Nouveau : {$newPassword}\n\n";
    
    echo "🔄 Rechargement de la configuration Laravel...\n";
    
    // Recharger la configuration
    $output = shell_exec('php artisan config:clear 2>&1');
    echo $output . "\n";
    
    echo "✅ Configuration rechargée !\n\n";
    
    echo "🧪 Test de l'envoi d'email...\n";
    echo "Exécutez : php test_email_auto.php\n";
    
} else {
    echo "❌ Impossible de trouver l'ancien mot de passe dans le fichier .env\n";
    echo "   Vérifiez manuellement le fichier .env\n";
    exit(1);
}
