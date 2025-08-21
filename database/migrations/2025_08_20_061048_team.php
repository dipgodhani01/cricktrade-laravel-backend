<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->string('team_id', 50)->unique()->index();
                $table->unsignedBigInteger('auction_id')->index();
                $table->string('team_logo')->nullable();
                $table->string('team_name');
                $table->integer('team_balance');
                $table->integer('remember_balance');
                $table->integer('reserve_balance');
                $table->integer('player_allow');
                $table->integer('player_buy');
                $table->integer('player_remember');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};