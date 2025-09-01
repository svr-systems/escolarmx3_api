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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('updated_by_id')->constrained('users');
            $table->foreignId('student_id')->constrained('students');
            $table->date('received_at');
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->boolean('is_original_left')->default(0);
            $table->tinyInteger('copies_count');
            $table->string('document_path',50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
