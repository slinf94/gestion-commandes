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
        // Vérifier si la table existe
        if (!Schema::hasTable('notifications')) {
            // Créer la table avec la structure personnalisée
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('system');
                $table->boolean('is_read')->default(false);
                $table->json('data')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
                $table->index('type');
                $table->index('is_read');
            });
        } else {
            // Vérifier et ajouter les colonnes manquantes
            $columns = Schema::getColumnListing('notifications');

            if (!in_array('user_id', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index('user_id');
                });
            }

            if (!in_array('title', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('title')->after('user_id');
                });
            }

            if (!in_array('message', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->text('message')->after('title');
                });
            }

            if (!in_array('type', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->string('type')->default('system')->after('message');
                    $table->index('type');
                });
            }

            if (!in_array('is_read', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->boolean('is_read')->default(false)->after('type');
                    $table->index('is_read');
                });
            }

            if (!in_array('data', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->json('data')->nullable()->after('is_read');
                });
            }

            // Vérifier si deleted_at existe
            if (!in_array('deleted_at', $columns)) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas supprimer la table, juste rollback si nécessaire
    }
};






