<?php

namespace App\Actions\Harvest;

use App\Models\Harvest;
use Illuminate\Support\Facades\DB;

class UpdateHarvestAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Harvest $harvest, array $data): Harvest
    {
        return DB::transaction(function () use ($harvest, $data) {
            $harvest->update($data);

            return $harvest->refresh();
        });
    }
}
