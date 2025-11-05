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
        // Vérifier si la colonne slug existe déjà
        if (!Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('slug', 255)->unique()->nullable()->after('name');
            });
        }
        
        // Générer des slugs pour les produits existants qui n'en ont pas
        $products = DB::table('products')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($products as $product) {
            $slug = \Illuminate\Support\Str::slug($product->name);
            $originalSlug = $slug;
            $counter = 1;
            
            // Vérifier l'unicité
            while (DB::table('products')->where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('products')->where('id', $product->id)->update(['slug' => $slug]);
        }
        
        // Rendre le slug obligatoire (si la colonne existe)
        if (Schema::hasColumn('products', 'slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('slug', 255)->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
