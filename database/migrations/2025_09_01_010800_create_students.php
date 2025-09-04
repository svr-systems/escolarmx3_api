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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('student_number', 15)->nullable();
            $table->foreignId('guardian_kinship_id')->nullable()->constrained('kinships');
            $table->string('guardian_name',100)->nullable();
            $table->string('guardian_phone',15)->nullable();
            $table->string('birth_certificate_path',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
