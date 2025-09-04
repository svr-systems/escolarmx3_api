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
        Schema::create('student_degrees', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('updated_by_id')->constrained('users');
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('level_id')->constrained('levels');
            $table->string('institution_name', 100);
            $table->string('name', 100);
            $table->foreignId('municipality_id')->constrained('municipalities');
            $table->date('start_at');
            $table->date('end_at');
            $table->string('license_number',20);
            $table->string('license_path',50)->nullable();
            $table->string('certificate_path',50)->nullable();
            $table->string('title_path',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_degrees');
    }
};
