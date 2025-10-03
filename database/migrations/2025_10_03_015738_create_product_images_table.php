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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('url'); // URL de l'image
            $table->enum('type', ['principale', 'secondaire', 'galerie'])->default('galerie');
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->string('alt_text')->nullable(); // Texte alternatif pour SEO
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['product_id', 'type']);
            $table->index(['product_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
