<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'admin@admin.com')->first();

if ($user) {
    $user->password = bcrypt('admin123');
    $user->save();
    
    echo "✅ Mot de passe réinitialisé avec succès!\n\n";
    echo "📧 Email: admin@admin.com\n";
    echo "🔑 Mot de passe: admin123\n";
} else {
    echo "❌ Utilisateur admin non trouvé!\n";
}
