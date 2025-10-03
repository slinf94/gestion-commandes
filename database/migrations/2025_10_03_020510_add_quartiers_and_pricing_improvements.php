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
        // Ajouter les colonnes de prix en gros et détail aux produits
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('price'); // Prix en gros
            $table->decimal('retail_price', 10, 2)->nullable()->after('wholesale_price'); // Prix détail
            $table->integer('min_wholesale_quantity')->default(10)->after('retail_price'); // Quantité min pour gros
        });

        // Modifier la colonne quartier dans users pour utiliser enum avec tous les quartiers
        Schema::table('users', function (Blueprint $table) {
            $table->string('quartier', 100)->nullable()->change();
            $table->string('photo')->nullable()->after('email'); // Photo de profil
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'retail_price', 'min_wholesale_quantity']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
