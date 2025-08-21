<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->string('user_id');
                $table->string('name')->index();
                $table->string('email')->unique();
                $table->enum('role', ['admin', 'user'])->default('user');
                $table->string('image')->nullable()->index();
                $table->timestamp('email_verified_at')->nullable()->index();
                $table->boolean('status')->default(false)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};