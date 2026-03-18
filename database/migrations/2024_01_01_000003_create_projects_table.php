<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('language')->default('PHP');
            $table->string('thumbnail')->nullable();
            $table->integer('xp_reward')->default(100);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable(); // For storing code snippets, stats, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
