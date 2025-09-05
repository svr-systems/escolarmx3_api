<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('settings', function (Blueprint $table) {
      $table->id();
      $table->boolean('is_active')->default(1);
      $table->timestamps();
      $table->foreignId('created_by_id')->constrained('users');
      $table->foreignId('updated_by_id')->constrained('users');
      $table->string('name', 100);
      $table->string('logo_path', 50)->nullable();
      $table->string('code', 10)->unique();
      $table->string('cct', 10)->unique();
    });
  }
  public function down(): void {
    Schema::dropIfExists('settings');
  }
};
