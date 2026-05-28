<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('pdf_public_id')->nullable()->after('pdf_path');
            $table->string('pdf_template')->nullable()->after('pdf_public_id');
            $table->timestamp('pdf_generated_at')->nullable()->after('pdf_template');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['pdf_public_id', 'pdf_template', 'pdf_generated_at']);
        });
    }
};
