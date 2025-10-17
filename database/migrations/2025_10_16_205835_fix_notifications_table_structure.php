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
        // Vérifier si la table notifications existe et a la bonne structure
        if (Schema::hasTable('notifications')) {
            // Vérifier si les colonnes nécessaires existent
            $columns = Schema::getColumnListing('notifications');

            if (!in_array('type', $columns)) {
                // Re créer la table notifications avec la structure Laravel standard
                Schema::dropIfExists('notifications');

                Schema::create('notifications', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->string('type');
                    $table->morphs('notifiable');
                    $table->text('data');
                    $table->timestamp('read_at')->nullable();
                    $table->timestamps();
                });
            }
        } else {
            // Créer la table notifications si elle n'existe pas
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
