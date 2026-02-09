<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('fa-code');
            $table->string('color')->default('#6366f1');
            $table->text('description')->nullable();
            $table->enum('category', ['frontend', 'backend', 'mobile', 'design', 'devops', 'security', 'ai']);
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->integer('required_level')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
