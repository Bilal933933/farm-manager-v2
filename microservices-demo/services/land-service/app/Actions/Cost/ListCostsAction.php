<?php

namespace App\Actions\Cost;

use App\Models\Cost;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class ListCostsAction
{
    /**
     * @return Collection<int, Cost>
     */
    public function execute(Season $season): Collection
    {
        return $season->costs()->latest()->get();
    }
}
