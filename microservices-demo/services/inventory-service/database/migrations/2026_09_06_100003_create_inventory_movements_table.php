<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->index();
            $table->foreignUuid('lot_id')->constrained('lots');
            $table->enum('type', ['harvest_in', 'purchase_in', 'sale_out', 'adjustment_in', 'adjustment_out']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
