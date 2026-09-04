<?php

namespace App\Actions\Cost;

use App\Models\Cost;
use App\Models\Season;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

class CreateCostAction
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Season $season, array $data): Cost
    {
        return DB::transaction(function () use ($season, $data) {
            if (! empty($data['product_id'])) {
                $data['unit_price'] = $data['unit_price'] ?? $this->inventory->getLastPrice($data['product_id']);
                $data['amount'] = $data['quantity'] * $data['unit_price'];
            }

            $cost = Cost::create([...$data, 'season_id' => $season->id]);

            return $cost->refresh();
        });
    }
}
