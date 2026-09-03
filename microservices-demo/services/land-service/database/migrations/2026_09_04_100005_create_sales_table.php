<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignUuid('harvest_id')->nullable()->constrained('harvests')->cascadeOnDelete();
            $table->uuid('product_id')->nullable()->index();
            $table->uuid('buyer_party_id')->index();
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 20)->default('kg');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 15, 2);
            $table->date('date');
            $table->enum('payment_status', ['paid', 'pending'])->default('paid');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
