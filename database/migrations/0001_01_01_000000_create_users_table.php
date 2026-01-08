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
        Schema::create('users', function (Blueprint $table) {
            // Use a custom primary key of user_id instead of the default id
            $table->bigIncrements('user_id');
            // Unique username used for login in addition to email
            $table->string('username')->unique();
            // The user's full name
            $table->string('full_name');
            // Email address must be unique
            $table->string('email')->unique();
            // Optional phone number
            $table->string('phone')->nullable();
            // Password hash
            $table->string('password');
            // Role or account type (e.g. admin or user) defaults to user
            $table->string('user_type')->default('user');
            // Timestamp for email verification
            $table->timestamp('email_verified_at')->nullable();
            // Remember token for "remember me" functionality
            $table->rememberToken();
            // Explicitly define created_at and updated_at instead of using $table->timestamps()
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // Use unsignedBigInteger to reference our custom user_id column
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
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
