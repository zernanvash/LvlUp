<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Resume-only private fields
            $table->string('phone_number')->nullable()->after('email');
            $table->string('home_address')->nullable()->after('phone_number');
            $table->string('city')->nullable()->after('home_address');
            $table->string('country')->nullable()->after('city');
            $table->string('website_url')->nullable()->after('country');

            // Technical skills (comma-separated or JSON)
            $table->text('technical_skills')->nullable()->after('github_url');

            // Resume details (private, used by resume builder)
            $table->string('resume_job_title')->nullable()->after('technical_skills');
            $table->text('resume_summary')->nullable()->after('resume_job_title');
            $table->text('work_experience')->nullable()->after('resume_summary');
            $table->text('education')->nullable()->after('work_experience');
            $table->text('certifications')->nullable()->after('education');
            $table->text('languages')->nullable()->after('certifications');

            // Public visibility toggles (JSON booleans)
            $table->json('visibility_settings')->nullable()->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number', 'home_address', 'city', 'country', 'website_url',
                'technical_skills',
                'resume_job_title', 'resume_summary', 'work_experience',
                'education', 'certifications', 'languages',
                'visibility_settings',
            ]);
        });
    }
};
