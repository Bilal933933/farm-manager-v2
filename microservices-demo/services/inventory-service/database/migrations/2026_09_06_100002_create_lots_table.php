<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->index();
            $table->foreignUuid('product_id')->constrained('products');
            $table->foreignUuid('warehouse_id')->constrained('warehouses');
            $table->enum('source_type', ['harvest', 'purchase', 'adjustment']);
            $table->uuid('source_id')->nullable()->index();
            $table->uuid('season_id')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->decimal('reserved_quantity', 12, 2)->default(0);
            $table->string('unit', 20);
            $table->decimal('cost_per_unit', 12, 2)->nullable();
            $table->date('harvest_date')->nullable();
            $table->enum('status', ['available', 'reserved', 'sold_out', 'expired'])->default('available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
