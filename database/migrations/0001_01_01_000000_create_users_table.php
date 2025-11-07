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
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nom', 100);
                $table->string('prenom', 100);
                $table->string('email', 255)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('numero_telephone', 20)->unique();
                $table->string('numero_whatsapp', 20)->nullable();
                $table->text('localisation')->nullable();
                $table->string('quartier', 100)->nullable();
                $table->string('ville', 100);
                $table->enum('role', ['client', 'admin', 'gestionnaire'])->default('client');
                $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
                $table->date('date_naissance')->nullable();
                $table->string('avatar', 255)->nullable();
                $table->boolean('two_factor_enabled')->default(false);
                $table->string('two_factor_secret', 255)->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
