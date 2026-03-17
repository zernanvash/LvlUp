<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // SQLite doesn't support ALTER COLUMN, so we recreate the tables with updated enums.

    public function up(): void
    {
        // ── Skills: add 'fullstack' to category, add 'uncommon' to rarity ──
        DB::statement('CREATE TABLE skills_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR NOT NULL,
            slug VARCHAR NOT NULL UNIQUE,
            icon VARCHAR NOT NULL DEFAULT "fa-code",
            color VARCHAR NOT NULL DEFAULT "#6366f1",
            description TEXT,
            category VARCHAR NOT NULL CHECK(category IN ("frontend","backend","mobile","design","devops","security","ai","fullstack")),
            rarity VARCHAR NOT NULL DEFAULT "common" CHECK(rarity IN ("common","uncommon","rare","epic","legendary")),
            required_level INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME,
            updated_at DATETIME
        )');
        DB::statement('INSERT INTO skills_new SELECT * FROM skills');
        DB::statement('DROP TABLE skills');
        DB::statement('ALTER TABLE skills_new RENAME TO skills');

        // ── Badges: add 'uncommon' to rarity ─────────────────────────────
        DB::statement('CREATE TABLE badges_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR NOT NULL,
            slug VARCHAR NOT NULL UNIQUE,
            description TEXT NOT NULL,
            icon VARCHAR NOT NULL DEFAULT "🏆",
            rarity VARCHAR NOT NULL DEFAULT "common" CHECK(rarity IN ("common","uncommon","rare","epic","legendary","mythic")),
            category VARCHAR,
            required_skill_id INTEGER REFERENCES skills(id) ON DELETE CASCADE,
            threshold INTEGER NOT NULL DEFAULT 1,
            xp_reward INTEGER NOT NULL DEFAULT 50,
            created_at DATETIME,
            updated_at DATETIME
        )');
        DB::statement('INSERT INTO badges_new SELECT * FROM badges');
        DB::statement('DROP TABLE badges');
        DB::statement('ALTER TABLE badges_new RENAME TO badges');
    }

    public function down(): void
    {
        // Revert skills category (remove fullstack) and rarity (remove uncommon)
        DB::statement('CREATE TABLE skills_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR NOT NULL,
            slug VARCHAR NOT NULL UNIQUE,
            icon VARCHAR NOT NULL DEFAULT "fa-code",
            color VARCHAR NOT NULL DEFAULT "#6366f1",
            description TEXT,
            category VARCHAR NOT NULL CHECK(category IN ("frontend","backend","mobile","design","devops","security","ai")),
            rarity VARCHAR NOT NULL DEFAULT "common" CHECK(rarity IN ("common","rare","epic","legendary")),
            required_level INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME,
            updated_at DATETIME
        )');
        DB::statement('INSERT INTO skills_new SELECT * FROM skills WHERE category != "fullstack" AND rarity != "uncommon"');
        DB::statement('DROP TABLE skills');
        DB::statement('ALTER TABLE skills_new RENAME TO skills');

        DB::statement('CREATE TABLE badges_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR NOT NULL,
            slug VARCHAR NOT NULL UNIQUE,
            description TEXT NOT NULL,
            icon VARCHAR NOT NULL DEFAULT "🏆",
            rarity VARCHAR NOT NULL DEFAULT "common" CHECK(rarity IN ("common","rare","epic","legendary","mythic")),
            category VARCHAR,
            required_skill_id INTEGER REFERENCES skills(id) ON DELETE CASCADE,
            threshold INTEGER NOT NULL DEFAULT 1,
            xp_reward INTEGER NOT NULL DEFAULT 50,
            created_at DATETIME,
            updated_at DATETIME
        )');
        DB::statement('INSERT INTO badges_new SELECT * FROM badges WHERE rarity != "uncommon"');
        DB::statement('DROP TABLE badges');
        DB::statement('ALTER TABLE badges_new RENAME TO badges');
    }
};
