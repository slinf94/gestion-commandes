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
        Schema::create('temporary_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['session_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_carts');
    }
};