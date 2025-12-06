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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('commercial_id')->nullable()->after('quartier');
            $table->foreign('commercial_id')->references('id')->on('users')->onDelete('set null');
            $table->index('commercial_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['commercial_id']);
            $table->dropIndex(['commercial_id']);
            $table->dropColumn('commercial_id');
        });
    }
};
