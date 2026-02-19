<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('gacha_currency_reward');
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->integer('gacha_currency_reward')->default(10);
        });
    }
};
