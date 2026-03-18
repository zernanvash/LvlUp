<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('claimed_date');
            $table->integer('day_number'); // Day 1, 2, 3... of streak
            $table->integer('xp_earned')->default(50);
            $table->integer('gacha_currency_earned')->default(20);
            $table->timestamps();
            
            $table->unique(['user_id', 'claimed_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_rewards');
    }
};
