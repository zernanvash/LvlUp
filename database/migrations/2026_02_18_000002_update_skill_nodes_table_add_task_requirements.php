<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skill_nodes', function (Blueprint $table) {
            $table->dropColumn('skill_point_cost');
            $table->json('task_requirements')->nullable()->after('required_level');
        });
    }

    public function down(): void
    {
        Schema::table('skill_nodes', function (Blueprint $table) {
            $table->dropColumn('task_requirements');
            $table->integer('skill_point_cost')->default(1);
        });
    }
};
