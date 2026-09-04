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
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable();
            $table->decimal('delivery_cost', 12, 2)->nullable();
            $table->string('currency', 10)->default('EGP');
            $table->string('buyer_name', 255)->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit', 'installment'])->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->enum('payment_status', ['paid', 'pending', 'partially_paid'])->default('pending');
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
