<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('issuer')->nullable();
            $table->date('issued_date')->nullable();
            $table->string('file_path');           // Cloudinary secure URL
            $table->string('file_public_id');      // Cloudinary public_id for deletion
            $table->string('file_type')->default('pdf'); // pdf, image
            $table->text('ai_summary')->nullable(); // Gemini-generated summary
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
