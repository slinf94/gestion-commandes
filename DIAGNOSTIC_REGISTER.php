<?php
// Script de diagnostic pour la création de compte

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSTIC CRÉATION DE COMPTE ===\n\n";

echo "1. Vérification de la table users...\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM users");
    echo "✅ Colonnes de la table users:\n";
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n2. Vérification des users existants...\n";
try {
    $count = DB::table('users')->count();
    echo "✅ Nombre total d'utilisateurs: $count\n";
    
    $recent = DB::table('users')
        ->select('id', 'nom', 'prenom', 'email', 'numero_telephone', 'role', 'status', 'created_at')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
    
    if ($recent->count() > 0) {
        echo "\n📋 5 derniers utilisateurs:\n";
        foreach ($recent as $user) {
            echo "   ID: {$user->id} | {$user->prenom} {$user->nom} | {$user->email} | Rôle: {$user->role} | Status: {$user->status}\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n3. Test de création d'un compte...\n";
try {
    $testEmail = 'test_' . time() . '@example.com';
    $testPhone = '+22796' . rand(100000, 999999);
    
    echo "📝 Création du compte test...\n";
    echo "   Email: $testEmail\n";
    echo "   Téléphone: $testPhone\n";
    
    $userId = DB::table('users')->insertGetId([
        'nom' => 'Test',
        'prenom' => 'Diagnostic',
        'email' => $testEmail,
        'numero_telephone' => $testPhone,
        'quartier' => 'Akpakpa',
        'password' => Hash::make('password123'),
        'role' => 'client',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✅ Compte créé avec succès! ID: $userId\n";
    
    // Supprimer le compte de test
    DB::table('users')->where('id', $userId)->delete();
    echo "🗑️ Compte de test supprimé\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur lors de la création: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n4. Vérification de la route API...\n";
try {
    $routes = Illuminate\Support\Facades\Route::getRoutes();
    $registerRoute = null;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'auth/register')) {
            $registerRoute = $route;
            break;
        }
    }
    
    if ($registerRoute) {
        echo "✅ Route trouvée: {$registerRoute->uri()}\n";
        echo "   Méthodes: " . implode(', ', $registerRoute->methods()) . "\n";
        echo "   Action: {$registerRoute->getActionName()}\n";
    } else {
        echo "❌ Route /api/v1/auth/register NON TROUVÉE!\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n5. Vérification du Helper NotificationHelper...\n";
try {
    if (class_exists('\App\Helpers\NotificationHelper')) {
        echo "✅ NotificationHelper existe\n";
    } else {
        echo "❌ NotificationHelper N'EXISTE PAS!\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n6. Dernières erreurs dans les logs...\n";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = file($logFile);
        $lastLines = array_slice($lines, -50);
        
        $errors = array_filter($lastLines, function($line) {
            return str_contains(strtolower($line), 'error') || 
                   str_contains(strtolower($line), 'register') ||
                   str_contains(strtolower($line), 'exception');
        });
        
        if (count($errors) > 0) {
            echo "⚠️ Erreurs récentes trouvées:\n";
            foreach (array_slice($errors, -10) as $error) {
                echo "   " . trim($error) . "\n";
            }
        } else {
            echo "✅ Aucune erreur récente dans les logs\n";
        }
    } else {
        echo "ℹ️ Pas de fichier de log\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
