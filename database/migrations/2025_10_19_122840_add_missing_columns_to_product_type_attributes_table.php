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
        Schema::table('product_type_attributes', function (Blueprint $table) {
            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('product_type_attributes', 'attribute_id')) {
                $table->unsignedBigInteger('attribute_id')->nullable()->after('product_type_id');
                $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            }
            if (!Schema::hasColumn('product_type_attributes', 'is_required')) {
                $table->boolean('is_required')->default(false)->after('attribute_id');
            }
            if (!Schema::hasColumn('product_type_attributes', 'is_filterable')) {
                $table->boolean('is_filterable')->default(true)->after('is_required');
            }
            if (!Schema::hasColumn('product_type_attributes', 'is_variant')) {
                $table->boolean('is_variant')->default(false)->after('is_filterable');
            }
            if (!Schema::hasColumn('product_type_attributes', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_variant');
            }
            if (!Schema::hasColumn('product_type_attributes', 'default_value')) {
                $table->json('default_value')->nullable()->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_type_attributes', function (Blueprint $table) {
            $table->dropForeign(['attribute_id']);
            $table->dropColumn([
                'attribute_id',
                'is_required',
                'is_filterable',
                'is_variant',
                'sort_order',
                'default_value'
            ]);
        });
    }
};
