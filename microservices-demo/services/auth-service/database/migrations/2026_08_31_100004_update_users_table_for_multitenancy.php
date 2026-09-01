<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // company_id nullable للـ Super Admin
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->after('company_id')->constrained('roles')->nullOnDelete();

            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes()->after('updated_at');

            // unique per company
            $table->dropUnique(['email']);
            $table->unique(['company_id', 'email']);
            $table->index('company_id');
            $table->index('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['company_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['company_id', 'role_id', 'phone', 'avatar', 'is_active', 'last_login_at']);
            $table->dropUnique(['company_id', 'email']);
            $table->unique('email');
        });
    }
};
