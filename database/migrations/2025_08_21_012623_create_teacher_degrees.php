<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_degrees', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(1);
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('level_id')->constrained('levels');
            $table->string('institution_name', 100);
            $table->string('name', 100);
            $table->string('license_number', 20);
            $table->string('license_path', 50)->nullable();
            $table->string('title_path', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_degrees');
    }
};
