<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('players')) {
            Schema::create('players', function (Blueprint $table) {
                $table->string('player_id', 50)->unique()->index();
                $table->unsignedInteger('index');
                $table->unsignedBigInteger('auction_id')->index();
                $table->unsignedBigInteger('sold_team_id')->nullable()->index();
                $table->string('sold_team')->nullable()->index();
                $table->string('player_logo')->nullable();
                $table->string('player_name');
                $table->integer('minimum_bid');
                $table->integer('final_bid')->nullable();
                $table->string('category');
                $table->string('phone', 10);
                $table->string('tshirt_size');
                $table->string('trouser_size')->nullable();
                $table->string('tshirt_name');
                $table->integer('tshirt_number');
                $table->tinyInteger('status')->default(0);

                $table->timestamps();

                // Foreign keys (optional if you want strict DB relations)
                // $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
                // $table->foreign('sold_team_id')->references('id')->on('teams')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};