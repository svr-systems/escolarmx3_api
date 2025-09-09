<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->after('email_verified_at');
            $table->foreignId('marital_status_id')->nullable()->constrained('marital_statuses')->after('password');
            $table->foreignId('contact_kinship_id')->nullable()->constrained('kinships')->after('ine_path');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('marital_status_id');
            $table->dropConstrainedForeignId('contact_kinship_id');
        });
    }
};
