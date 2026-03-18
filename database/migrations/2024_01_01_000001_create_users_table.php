<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('title')->default('Novice Developer');
            $table->integer('level')->default(1);
            $table->integer('xp')->default(0);
            $table->integer('total_xp')->default(0);
            $table->integer('skill_points')->default(5);
            $table->string('rank')->default('Bronze');
            $table->integer('gacha_currency')->default(160); // Primogems-style
            $table->integer('streak_days')->default(0);
            $table->date('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
