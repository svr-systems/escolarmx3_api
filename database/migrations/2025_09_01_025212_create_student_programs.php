<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('student_programs', function (Blueprint $table) {
      $table->id();
      $table->boolean('is_active')->default(1);
      $table->timestamps();
      $table->foreignId('created_by_id')->constrained('users');
      $table->foreignId('updated_by_id')->constrained('users');
      $table->foreignId('student_id')->constrained('students');
      $table->foreignId('program_id')->constrained('programs');
      $table->foreignId('cycle_entry_id')->constrained('cycles');
      $table->boolean('is_equivalency')->default(0);
      $table->string('equivalency_path', 50)->nullable()->default(null);
      $table->foreignId('cycle_dropout_id')->nullable()->constrained('cycles');
      $table->foreignId('cycle_reentry_id')->nullable()->constrained('cycles');
      $table->foreignId('cycle_graduated_id')->nullable()->constrained('cycles');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('student_programs');
  }
};
