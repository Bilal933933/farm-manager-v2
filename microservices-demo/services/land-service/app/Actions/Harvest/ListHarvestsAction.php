<?php

namespace App\Actions\Harvest;

use App\Models\Harvest;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class ListHarvestsAction
{
    /**
     * @return Collection<int, Harvest>
     */
    public function execute(Season $season): Collection
    {
        return $season->harvests()->latest()->get();
    }
}
