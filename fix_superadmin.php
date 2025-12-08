<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== VERIFICATION SUPERADMIN ===\n\n";

try {
    DB::connection()->getPdo();
    echo "[OK] Base de donnees connectee\n";
} catch (Exception $e) {
    die("[ERREUR] " . $e->getMessage() . "\n");
}

$email = 'superadmin@monprojet.com';
$password = 'SuperAdmin123!';

$user = User::where('email', $email)->first();

if ($user) {
    echo "[TROUVE] SuperAdmin existe (ID: {$user->id})\n";
    echo "Role actuel: {$user->role}\n";
    echo "Status: {$user->status}\n";

    // Verifier mot de passe
    if (Hash::check($password, $user->password)) {
        echo "[OK] Mot de passe correct\n";
    } else {
        echo "[FIX] Reinitialisation du mot de passe...\n";
        $user->password = Hash::make($password);
        $user->save();
    }

    // Corriger role si necessaire
    if ($user->role !== 'super-admin') {
        echo "[FIX] Correction du role...\n";
        $user->role = 'super-admin';
        $user->save();
    }

    // Corriger status si necessaire
    if ($user->status !== 'active') {
        echo "[FIX] Activation du compte...\n";
        $user->status = 'active';
        $user->save();
    }

} else {
    echo "[CREATION] SuperAdmin...\n";
    User::create([
        'nom' => 'Super',
        'prenom' => 'Admin',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'super-admin',
        'status' => 'active',
        'email_verified_at' => now(),
        'numero_telephone' => '+226 70 99 99 99',
        'localisation' => 'Ouagadougou',
        'quartier' => 'Secteur 1',
    ]);
    echo "[OK] SuperAdmin cree!\n";
}

echo "\n=== IDENTIFIANTS ===\n";
echo "Email: superadmin@monprojet.com\n";
echo "Mot de passe: SuperAdmin123!\n";
echo "URL: http://localhost:8000/admin/login\n";
