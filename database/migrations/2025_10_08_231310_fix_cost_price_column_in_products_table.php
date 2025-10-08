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
        Schema::table('products', function (Blueprint $table) {
            // Modifier le champ cost_price pour supporter de plus grandes valeurs
            // Changement de decimal(10,2) vers decimal(15,2) pour supporter jusqu'à 999,999,999,999,999.99
            $table->decimal('cost_price', 15, 2)->nullable()->change();

            // Optionnel : modifier aussi les autres champs de prix pour cohérence
            $table->decimal('price', 15, 2)->nullable()->change();
            $table->decimal('wholesale_price', 15, 2)->nullable()->change();
            $table->decimal('retail_price', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revenir aux valeurs originales
            $table->decimal('cost_price', 10, 2)->nullable()->change();
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->decimal('wholesale_price', 10, 2)->nullable()->change();
            $table->decimal('retail_price', 10, 2)->nullable()->change();
        });
    }
};
