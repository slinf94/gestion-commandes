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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->decimal('wholesale_price', 15, 2)->nullable();
            $table->decimal('retail_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->integer('min_wholesale_quantity')->default(10);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->string('sku', 100)->nullable()->unique();
            $table->string('barcode', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('range', 100)->nullable();
            $table->string('format', 100)->nullable();
            $table->string('type_accessory', 100)->nullable();
            $table->string('compatibility', 100)->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['active', 'inactive', 'out_of_stock', 'discontinued', 'draft'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status', 'products_status_index');
            $table->index('category_id', 'products_category_id_index');
            $table->index('product_type_id', 'products_product_type_id_index');
            $table->index('brand', 'products_brand_index');
            $table->index('range', 'products_range_index');
            $table->index('deleted_at', 'products_deleted_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
