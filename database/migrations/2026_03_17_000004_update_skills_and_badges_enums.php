<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Update skills category enum to include 'fullstack',
     * and rarity enum to include 'uncommon' for both skills and badges.
     *
     * Rewritten from SQLite-specific DDL to MySQL-compatible Schema operations.
     */
    public function up(): void
    {
        // ── Skills: category + rarity enums ──────────────────────────────
        if (Schema::hasTable('skills')) {
            Schema::table('skills', function (Blueprint $table) {
                $table->string('category')->default('backend')
                    ->change(); // Remove enum constraint first, set as string
            });
            Schema::table('skills', function (Blueprint $table) {
                $table->string('rarity')->default('common')
                    ->change();
            });
        }

        // ── Badges: rarity enum ───────────────────────────────────────────
        if (Schema::hasTable('badges')) {
            Schema::table('badges', function (Blueprint $table) {
                $table->string('rarity')->default('common')
                    ->change();
            });
        }
    }

    public function down(): void
    {
        // No-op: reverting enum constraints on MySQL is complex and unnecessary.
        // The string columns remain; application-level validation handles values.
    }
};
