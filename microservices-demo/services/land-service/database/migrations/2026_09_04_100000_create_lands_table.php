<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');
        }

        Schema::create('lands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->index();
            $table->string('slug', 150)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('area', 12, 2);
            $table->enum('area_unit', ['feddan', 'hectare', 'dunum', 'sq_meter'])->default('feddan');
            $table->json('map_coordinates')->nullable();
            $table->enum('ownership_type', ['owned', 'rented_in', 'shared']);
            $table->uuid('owner_party_id')->index();
            $table->enum('status', ['active', 'inactive', 'under_contract'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lands');
    }
};
