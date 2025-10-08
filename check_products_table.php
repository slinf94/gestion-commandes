<?php

/**
 * Script pour vérifier la structure de la table products
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📊 STRUCTURE DE LA TABLE PRODUCTS\n";
echo "==================================\n\n";

try {
    $columns = DB::select('DESCRIBE products');

    echo "Colonnes de la table products :\n";
    foreach ($columns as $column) {
        echo sprintf(
            "%-20s | %-15s | %-5s | %-10s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Default ?? 'NULL'
        );
    }

    echo "\n🔍 Analyse des colonnes numériques :\n";
    foreach ($columns as $column) {
        if (strpos($column->Type, 'int') !== false || strpos($column->Type, 'decimal') !== false) {
            echo "   - {$column->Field} : {$column->Type}\n";

            // Vérifier les limites pour les types numériques
            if (strpos($column->Type, 'int') !== false) {
                if (strpos($column->Type, 'tinyint') !== false) {
                    echo "     Limite : -128 à 127 (ou 0 à 255 si UNSIGNED)\n";
                } elseif (strpos($column->Type, 'smallint') !== false) {
                    echo "     Limite : -32,768 à 32,767 (ou 0 à 65,535 si UNSIGNED)\n";
                } elseif (strpos($column->Type, 'mediumint') !== false) {
                    echo "     Limite : -8,388,608 à 8,388,607 (ou 0 à 16,777,215 si UNSIGNED)\n";
                } elseif (strpos($column->Type, 'int') !== false) {
                    echo "     Limite : -2,147,483,648 à 2,147,483,647 (ou 0 à 4,294,967,295 si UNSIGNED)\n";
                } elseif (strpos($column->Type, 'bigint') !== false) {
                    echo "     Limite : Très grande (jusqu'à 19 chiffres)\n";
                }
            }
        }
    }

    echo "\n💡 Problème identifié :\n";
    echo "   La valeur 200000000 (200 millions) dépasse probablement la limite du champ cost_price\n";
    echo "   Solution : Modifier le type de données du champ cost_price\n\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
