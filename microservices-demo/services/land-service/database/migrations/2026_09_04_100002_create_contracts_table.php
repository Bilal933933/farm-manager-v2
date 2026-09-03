<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('land_id')->constrained('lands')->cascadeOnDelete();
            $table->enum('contract_type', ['rent_in', 'rent_out', 'sharecropping', 'management']);
            $table->uuid('counterparty_party_id')->index();
            $table->uuid('owner_party_id')->nullable()->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('financial_value', 15, 2)->nullable();
            $table->decimal('revenue_share_percentage', 5, 2)->nullable();
            $table->enum('payment_terms', ['annual', 'semi_annual', 'quarterly', 'monthly', 'lump_sum'])->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
