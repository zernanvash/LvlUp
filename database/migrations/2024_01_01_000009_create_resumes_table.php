<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('job_title');
            $table->text('target_keywords')->nullable();
            $table->text('job_description')->nullable();
            $table->json('selected_project_ids')->nullable();
            $table->json('selected_skill_ids')->nullable();
            $table->string('pdf_path')->nullable();
            $table->integer('match_score')->nullable(); // AI-calculated match percentage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
