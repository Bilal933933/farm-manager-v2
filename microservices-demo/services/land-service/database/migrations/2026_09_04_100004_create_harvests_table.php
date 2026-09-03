<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->uuid('product_id')->nullable()->index();
            $table->date('date');
            $table->decimal('total_quantity', 12, 2);
            $table->string('unit', 20)->default('kg');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
