<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "\n🔧 CORRECTION FINALE DU SUPER ADMIN\n\n";

$user = User::where('email', 'super@admin.com')->first();
$user->roles()->detach();

// Attacher seulement super-admin
$role = Role::where('slug', 'super-admin')->first();
$user->attachRole($role);

echo "✅ Super Admin corrigé: seulement le rôle 'super-admin' attaché\n\n";

use App\Helpers\AdminMenuHelper;

echo "VÉRIFICATION:\n";
$roles = AdminMenuHelper::getAllRoles($user);
echo sprintf("  Rôles: %s\n", implode(', ', $roles));

echo "\n";

