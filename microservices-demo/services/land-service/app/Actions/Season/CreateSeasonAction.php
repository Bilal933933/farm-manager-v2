<?php

namespace App\Actions\Season;

use App\Models\Land;
use App\Models\Season;
use Illuminate\Support\Facades\DB;

class CreateSeasonAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Land $land, array $data): Season
    {
        return DB::transaction(function () use ($land, $data) {
            $season = Season::create([...$data, 'land_id' => $land->id]);

            return $season->refresh();
        });
    }
}
