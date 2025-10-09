<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\PasswordResetNotification;

// Configuration de base
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test de récupération de mot de passe ===\n\n";

// Demander l'email à l'utilisateur
echo "Entrez l'email de l'utilisateur pour tester la récupération de mot de passe: ";
$email = trim(fgets(STDIN));

if (empty($email)) {
    echo "❌ Email vide. Test annulé.\n";
    exit(1);
}

try {
    // Vérifier si l'utilisateur existe
    $user = User::where('email', $email)->first();

    if (!$user) {
        echo "❌ Aucun utilisateur trouvé avec l'email: $email\n";
        exit(1);
    }

    echo "✅ Utilisateur trouvé: {$user->first_name} {$user->last_name}\n";

    // Générer un token de réinitialisation
    $token = Str::random(64);
    echo "✅ Token généré: " . substr($token, 0, 20) . "...\n";

    // Supprimer les anciens tokens pour cet email
    DB::table('password_reset_tokens')->where('email', $email)->delete();
    echo "✅ Anciens tokens supprimés\n";

    // Insérer le nouveau token
    DB::table('password_reset_tokens')->insert([
        'email' => $email,
        'token' => $token,
        'created_at' => now()
    ]);
    echo "✅ Nouveau token inséré en base\n";

    // Envoyer l'email de réinitialisation
    echo "📧 Envoi de l'email de réinitialisation...\n";
    $user->notify(new PasswordResetNotification($token));

    echo "✅ Email envoyé avec succès à: $email\n";
    echo "✅ Test terminé avec succès!\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
