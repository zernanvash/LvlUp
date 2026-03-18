<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->default('🏆');
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary', 'mythic'])->default('common');
            $table->string('category')->nullable(); // 'skill', 'project', 'streak', etc.
            $table->foreignId('required_skill_id')->nullable()->constrained('skills')->onDelete('cascade');
            $table->integer('threshold')->default(1); // Number of projects/days/etc
            $table->integer('xp_reward')->default(50);
            $table->integer('gacha_currency_reward')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
