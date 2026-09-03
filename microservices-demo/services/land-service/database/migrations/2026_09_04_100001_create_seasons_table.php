<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('land_id')->constrained('lands')->cascadeOnDelete();
            $table->uuid('product_id')->index();
            $table->string('name', 150)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('expected_yield', 12, 2)->nullable();
            $table->enum('status', ['preparing', 'planted', 'growing', 'harvesting', 'completed', 'canceled'])->default('preparing');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
