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
        // Vérifier si la colonne id existe et si elle est de type UUID
        if (Schema::hasTable('notifications')) {
            $columns = Schema::getColumnListing('notifications');
            
            // Si la colonne id existe mais n'est pas auto-increment, la modifier
            if (in_array('id', $columns)) {
                // Vérifier le type de la colonne id
                $columnType = DB::select("SHOW COLUMNS FROM notifications WHERE Field = 'id'");
                
                if (!empty($columnType)) {
                    $definition = $columnType[0];
                    $type = $definition->Type;
                    $extra = strtolower($definition->Extra ?? '');
                    
                    $isAlreadyAutoIncrement = strpos($type, 'int') !== false && str_contains($extra, 'auto_increment');

                    // Si c'est un UUID ou un char, le convertir en bigint auto-increment
                    if (!$isAlreadyAutoIncrement && (strpos($type, 'char') !== false || strpos($type, 'uuid') !== false)) {
                        // Supprimer les anciennes données (notifications Laravel standard)
                        // Ces notifications ne sont pas utilisées par notre système personnalisé
                        DB::table('notifications')->whereNull('user_id')->orWhereNull('title')->delete();
                        
                        // Supprimer la clé primaire
                        try {
                            DB::statement('ALTER TABLE notifications DROP PRIMARY KEY');
                        } catch (\Exception $e) {
                            // Ignorer si la clé primaire n'existe pas
                        }
                        
                        // Supprimer la colonne id et la recréer en bigint auto-increment
                        DB::statement('ALTER TABLE notifications DROP COLUMN id');
                        DB::statement('ALTER TABLE notifications ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
                    } elseif (!$isAlreadyAutoIncrement && strpos($type, 'int') !== false && ! str_contains($extra, 'auto_increment')) {
                        // Si c'est un int mais pas auto-increment, ajouter auto-increment
                        DB::statement('ALTER TABLE notifications MODIFY COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
                    }
                }
            }
            
            // S'assurer que les colonnes nécessaires existent
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
        // Ne pas rollback automatiquement pour éviter de perdre des données
    }
};

