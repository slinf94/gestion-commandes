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
            // Rendre la colonne attribute_name nullable si elle existe
            if (Schema::hasColumn('product_type_attributes', 'attribute_name')) {
                $table->string('attribute_name')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_type_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('product_type_attributes', 'attribute_name')) {
                $table->string('attribute_name')->nullable(false)->change();
            }
        });
    }
};
