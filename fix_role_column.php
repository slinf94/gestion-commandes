<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Modification de la colonne 'role' dans la table users...\n\n";

try {
    // Modifier la colonne role pour ajouter 'commercial'
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire', 'vendeur', 'commercial') DEFAULT 'client'");
    echo "✅ Colonne 'role' modifiée avec succès !\n";
    
    // Vérifier si la colonne commercial_id existe déjà
    $columns = DB::select("SHOW COLUMNS FROM users WHERE Field = 'commercial_id'");
    
    if (empty($columns)) {
        echo "\n🔧 Ajout de la colonne 'commercial_id'...\n";
        DB::statement("ALTER TABLE users ADD COLUMN commercial_id BIGINT UNSIGNED NULL AFTER status");
        DB::statement("ALTER TABLE users ADD FOREIGN KEY (commercial_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "✅ Colonne 'commercial_id' ajoutée avec succès !\n";
    } else {
        echo "\n✅ La colonne 'commercial_id' existe déjà.\n";
    }
    
    echo "\n🎉 Modifications terminées avec succès !\n";
    echo "\n💡 Vous pouvez maintenant exécuter : php artisan db:seed --class=UserSeeder\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

