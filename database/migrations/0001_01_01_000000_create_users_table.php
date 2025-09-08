<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->foreignId('created_by_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_id')->nullable()->constrained('users');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name');
            $table->string('surname_p', 25);
            $table->string('surname_m', 25)->nullable();
            $table->string('curp', 18)->unique();
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password',60)->nullable()->default(null);
            $table->string('avatar_path', 50)->nullable();
            $table->string('curp_path', 50)->nullable();
            $table->string('birth_certificate_path',50)->nullable();
            $table->string('ine_path',50)->nullable();
            $table->string('contact_name',100)->nullable();
            $table->string('contact_phone',15)->nullable();
            $table->rememberToken();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
