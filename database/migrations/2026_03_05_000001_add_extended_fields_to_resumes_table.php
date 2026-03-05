<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds all new fields introduced by the AI Resume Writer v2 upgrade.
     *
     * New input columns  : phone, location, linked_in, github_url, tone,
     *                      work_experience, education_details, certifications,
     *                      spoken_languages, bio_seed
     *
     * The resume_data JSON column (added in a previous migration) now stores
     * the expanded 9-field AI output:
     *   headline, summary, skills, experience, projects,
     *   education, certifications, languages, achievements
     */
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            // ── Personal / contact ──────────────────────────────────────────
            $table->string('phone')->nullable()->after('template');
            $table->string('location')->nullable()->after('phone');
            $table->string('linked_in')->nullable()->after('location');
            $table->string('github_url')->nullable()->after('linked_in');

            // ── Writing preferences ─────────────────────────────────────────
            $table->string('tone')->default('professional')->after('github_url');

            // ── Rich input for AI ───────────────────────────────────────────
            $table->text('work_experience')->nullable()->after('tone');
            $table->text('education_details')->nullable()->after('work_experience');
            $table->text('certifications')->nullable()->after('education_details');
            $table->string('spoken_languages')->nullable()->after('certifications');
            $table->text('bio_seed')->nullable()->after('spoken_languages');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'location', 'linked_in', 'github_url',
                'tone', 'work_experience', 'education_details',
                'certifications', 'spoken_languages', 'bio_seed',
            ]);
        });
    }
};
