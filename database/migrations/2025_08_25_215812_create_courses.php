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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreignId('created_by_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_id')->nullable()->constrained('users');
            $table->foreignId('program_id')->nullable()->constrained('programs');
            $table->string('name',100);
            $table->foreignId('course_type_id')->nullable()->constrained('course_types');
            $table->string('code', 10)->unique();
            $table->string('alt_code', 10)->unique()->nullable();
            $table->decimal('credits',5,2);
            $table->smallInteger('session_minutes');
            $table->tinyInteger('term');
            $table->foreignId('prerequisite_course_id')->nullable()->constrained('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
