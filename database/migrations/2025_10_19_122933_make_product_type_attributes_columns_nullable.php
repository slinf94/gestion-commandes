<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_type_attributes', function (Blueprint $table) {
            // Rendre toutes les colonnes optionnelles si elles existent
            $columns = ['attribute_name', 'attribute_slug', 'attribute_type', 'attribute_value'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('product_type_attributes', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_type_attributes', function (Blueprint $table) {
            $columns = ['attribute_name', 'attribute_slug', 'attribute_type', 'attribute_value'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('product_type_attributes', $column)) {
                    $table->string($column)->nullable(false)->change();
                }
            }
        });
    }
};
