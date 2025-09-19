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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_type_attribute_id')->constrained()->onDelete('cascade');
            $table->text('attribute_value');
            $table->decimal('numeric_value', 15, 4)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'product_type_attribute_id']);
            $table->index('numeric_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
