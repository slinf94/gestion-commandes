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
        // Modifier l'enum pour ajouter 'vendeur'
        // Note: MySQL ne permet pas de modifier un ENUM directement, il faut recréer la colonne
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire', 'vendeur') DEFAULT 'client'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retirer 'vendeur' de l'enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'admin', 'gestionnaire') DEFAULT 'client'");
    }
};
