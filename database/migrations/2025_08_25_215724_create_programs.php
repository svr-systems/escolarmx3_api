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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreignId('created_by_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_id')->nullable()->constrained('users');
            $table->foreignId('campus_id')->nullable()->constrained('campuses');
            $table->string('name',100);
            $table->string('code', 10)->unique();
            $table->date('issued_at');
            $table->foreignId('accreditation_id')->nullable()->constrained('accreditations');
            $table->foreignId('modality_id')->nullable()->constrained('modalities');
            $table->foreignId('shift_id')->nullable()->constrained('shifts');
            $table->string('responsible_curp', 18)->nullable();
            $table->year('plan_year');
            $table->foreignId('level_id')->nullable()->constrained('levels');
            $table->foreignId('term_id')->nullable()->constrained('terms');
            $table->tinyInteger('terms_count');
            $table->decimal('grade_min',5,2);
            $table->decimal('grade_max',5,2);
            $table->decimal('grade_pass',5,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
