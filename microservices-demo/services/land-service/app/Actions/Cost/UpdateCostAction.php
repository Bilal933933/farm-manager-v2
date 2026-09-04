<?php

namespace App\Actions\Cost;

use App\Models\Cost;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class UpdateCostAction
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Cost $cost, array $data): Cost
    {
        return DB::transaction(function () use ($cost, $data) {
            $productId = $data['product_id'] ?? $cost->product_id;

            if (! empty($productId)) {
                $quantity = $data['quantity'] ?? $cost->quantity;
                $unitPrice = $data['unit_price'] ?? $cost->unit_price ?? $this->inventory->getLastPrice($productId);

                $data['unit_price'] = $unitPrice;
                $data['amount'] = $quantity * $unitPrice;
            }

            $cost->update($data);

            return $cost->refresh();
        });
    }
}
