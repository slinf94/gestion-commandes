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
        // Corriger la table products
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
            $table->decimal('cost_price', 15, 2)->nullable()->change();
        });

        // Corriger la table orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax_amount', 15, 2)->default(0)->change();
            $table->decimal('discount_amount', 15, 2)->default(0)->change();
            $table->decimal('shipping_cost', 15, 2)->default(0)->change();
            $table->decimal('total_amount', 15, 2)->change();
        });

        // Corriger la table order_items
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total_price', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir aux anciennes précisions
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('cost_price', 10, 2)->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->change();
            $table->decimal('tax_amount', 10, 2)->default(0)->change();
            $table->decimal('discount_amount', 10, 2)->default(0)->change();
            $table->decimal('shipping_cost', 10, 2)->default(0)->change();
            $table->decimal('total_amount', 10, 2)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->change();
            $table->decimal('total_price', 10, 2)->change();
        });
    }
};
