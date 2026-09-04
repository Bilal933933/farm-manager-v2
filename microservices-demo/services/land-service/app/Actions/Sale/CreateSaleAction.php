<?php

namespace App\Actions\Sale;

use App\Models\Sale;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class CreateSaleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Season $season, array $data): Sale
    {
        return DB::transaction(function () use ($season, $data) {
            $data['total_price'] = $data['quantity'] * $data['unit_price']
                - ($data['discount_amount'] ?? 0)
                + ($data['tax_amount'] ?? 0)
                + ($data['delivery_cost'] ?? 0);

            $sale = Sale::create([...$data, 'season_id' => $season->id]);

            return $sale->refresh();
        });
    }
}
