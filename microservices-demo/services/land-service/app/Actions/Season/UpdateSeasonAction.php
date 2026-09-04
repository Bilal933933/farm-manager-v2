<?php

namespace App\Actions\Season;

use App\Models\Season;
use Illuminate\Support\Facades\DB;

class UpdateSeasonAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Season $season, array $data): Season
    {
        return DB::transaction(function () use ($season, $data) {
            $season->update($data);

            return $season->refresh();
        });
    }
}
