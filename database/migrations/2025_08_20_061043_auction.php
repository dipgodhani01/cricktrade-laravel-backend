<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        if (!Schema::hasTable('auctions')) {
            Schema::create('auctions', function (Blueprint $table) {
                $table->string('auction_id', 50)->unique()->index();
                $table->string('user_id', 40)->index();
                $table->string('auction_logo')->nullable();
                $table->string('auction_name');
                $table->date('auction_date');
                $table->string('sports_type');
                $table->integer('point_perteam');
                $table->integer('minimum_bid');
                $table->integer('bid_increment');
                $table->integer('player_perteam');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();

                // Foreign key (optional if you want)
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};