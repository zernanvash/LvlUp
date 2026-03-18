<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gacha_currency', 'streak_days', 'skill_points']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('gacha_currency')->default(160);
            $table->integer('streak_days')->default(0);
            $table->integer('skill_points')->default(5);
        });
    }
};
