<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'projects_user_created_idx');
            $table->index(['user_id', 'project_type'], 'projects_user_type_idx');
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'resumes_user_created_idx');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index(['user_id', 'issued_date'], 'certificates_user_issued_idx');
        });

        Schema::table('user_badges', function (Blueprint $table) {
            $table->index(['user_id', 'is_displayed', 'updated_at'], 'user_badges_display_idx');
        });

        Schema::table('user_skill_nodes', function (Blueprint $table) {
            $table->index(['user_id', 'unlocked_at'], 'user_skill_nodes_unlocked_idx');
        });

        Schema::table('skill_nodes', function (Blueprint $table) {
            $table->index(['parent_node_id', 'tier'], 'skill_nodes_parent_tier_idx');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_user_created_idx');
            $table->dropIndex('projects_user_type_idx');
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropIndex('resumes_user_created_idx');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex('certificates_user_issued_idx');
        });

        Schema::table('user_badges', function (Blueprint $table) {
            $table->dropIndex('user_badges_display_idx');
        });

        Schema::table('user_skill_nodes', function (Blueprint $table) {
            $table->dropIndex('user_skill_nodes_unlocked_idx');
        });

        Schema::table('skill_nodes', function (Blueprint $table) {
            $table->dropIndex('skill_nodes_parent_tier_idx');
        });
    }
};
