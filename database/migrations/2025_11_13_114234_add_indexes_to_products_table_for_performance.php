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
            // Ajouter des index sur les colonnes fréquemment utilisées dans les filtres
            // Vérifier d'abord si les index n'existent pas déjà
            $indexes = DB::select("SHOW INDEX FROM products WHERE Key_name != 'PRIMARY'");
            $existingIndexNames = array_column($indexes, 'Key_name');
            
            if (!in_array('products_status_index', $existingIndexNames)) {
                $table->index('status', 'products_status_index');
            }
            
            if (!in_array('products_category_id_index', $existingIndexNames)) {
                $table->index('category_id', 'products_category_id_index');
            }
            
            if (!in_array('products_product_type_id_index', $existingIndexNames)) {
                $table->index('product_type_id', 'products_product_type_id_index');
            }
            
            if (!in_array('products_brand_index', $existingIndexNames)) {
                $table->index('brand', 'products_brand_index');
            }
            
            if (!in_array('products_range_index', $existingIndexNames)) {
                $table->index('range', 'products_range_index');
            }
            
            if (!in_array('products_deleted_at_index', $existingIndexNames)) {
                $table->index('deleted_at', 'products_deleted_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_product_type_id_index');
            $table->dropIndex('products_brand_index');
            $table->dropIndex('products_range_index');
            $table->dropIndex('products_deleted_at_index');
        });
    }
};
