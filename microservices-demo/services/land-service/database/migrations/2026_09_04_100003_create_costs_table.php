<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->enum('cost_type', ['seeds', 'fertilizer', 'pesticides', 'labor', 'equipment', 'irrigation', 'transport', 'rent', 'other']);
            $table->uuid('product_id')->nullable()->index();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->enum('payment_status', ['paid', 'pending'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
