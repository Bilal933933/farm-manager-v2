<?php

namespace App\Actions\Harvest;

use App\Models\Harvest;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class CreateHarvestAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Season $season, array $data): Harvest
    {
        return DB::transaction(function () use ($season, $data) {
            $harvest = Harvest::create([...$data, 'season_id' => $season->id]);

            return $harvest->refresh();
        });
    }
}
