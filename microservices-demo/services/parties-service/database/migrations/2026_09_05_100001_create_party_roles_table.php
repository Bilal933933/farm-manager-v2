<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('party_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('party_id')->constrained('parties')->cascadeOnDelete();
            $table->enum('role', ['supplier', 'farmer', 'owner', 'tenant', 'buyer', 'lessor', 'contractor'])->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['party_id', 'role']);

            // Index for filtering by role type
            $table->index(['party_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('party_roles');
    }
};
