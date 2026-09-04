<?php

namespace App\Actions\Sale;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class UpdateSaleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Sale $sale, array $data): Sale
    {
        return DB::transaction(function () use ($sale, $data) {
            $quantity = $data['quantity'] ?? $sale->quantity;
            $unitPrice = $data['unit_price'] ?? $sale->unit_price;
            $discount = $data['discount_amount'] ?? $sale->discount_amount ?? 0;
            $tax = $data['tax_amount'] ?? $sale->tax_amount ?? 0;
            $delivery = $data['delivery_cost'] ?? $sale->delivery_cost ?? 0;

            if (array_intersect_key($data, array_flip(['quantity', 'unit_price', 'discount_amount', 'tax_amount', 'delivery_cost']))) {
                $data['total_price'] = $quantity * $unitPrice - $discount + $tax + $delivery;
            }

            $sale->update($data);

            return $sale->refresh();
        });
    }
}
