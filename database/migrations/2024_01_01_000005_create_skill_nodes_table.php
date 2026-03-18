<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_node_id')->nullable()->constrained('skill_nodes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('x_position')->default(50); // Percentage
            $table->integer('y_position')->default(50); // Pixels
            $table->enum('tier', ['core', 'basic', 'advanced', 'master', 'legendary'])->default('basic');
            $table->integer('required_level')->default(1);
            $table->integer('skill_point_cost')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_nodes');
    }
};
