<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ajouter les nouveaux champs pour téléphones et accessoires
            $table->string('brand', 100)->nullable()->after('barcode');
            $table->string('range', 100)->nullable()->after('brand');
            $table->string('format', 100)->nullable()->after('range');
            $table->string('type_accessory', 100)->nullable()->after('format');
            $table->string('compatibility', 100)->nullable()->after('type_accessory');
        });

        // Modifier l'enum status pour ajouter 'draft'
        // Note: MySQL nécessite une modification manuelle de l'enum
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'out_of_stock', 'discontinued', 'draft') DEFAULT 'active'");
        
        // Modifier SKU pour le rendre nullable (génération automatique)
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand', 'range', 'format', 'type_accessory', 'compatibility']);
        });

        // Revenir à l'enum original (sans draft)
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'out_of_stock', 'discontinued') DEFAULT 'active'");
        
        // Remettre SKU comme non-nullable (si nécessaire)
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 100)->nullable(false)->change();
        });
    }
};
