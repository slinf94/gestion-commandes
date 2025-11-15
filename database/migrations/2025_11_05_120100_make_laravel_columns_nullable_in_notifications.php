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
        if (Schema::hasTable('notifications')) {
            $columns = Schema::getColumnListing('notifications');
            
            // Rendre nullable les colonnes Laravel Notifications qui ne sont pas utilisées
            if (in_array('notifiable_type', $columns)) {
                DB::statement('ALTER TABLE notifications MODIFY COLUMN notifiable_type VARCHAR(255) NULL');
            }
            
            if (in_array('notifiable_id', $columns)) {
                DB::statement('ALTER TABLE notifications MODIFY COLUMN notifiable_id BIGINT UNSIGNED NULL');
            }
            
            if (in_array('read_at', $columns)) {
                // read_at est déjà nullable normalement, mais on s'assure
                DB::statement('ALTER TABLE notifications MODIFY COLUMN read_at TIMESTAMP NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas rollback automatiquement
    }
};













