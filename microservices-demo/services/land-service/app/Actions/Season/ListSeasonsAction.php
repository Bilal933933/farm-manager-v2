<?php

namespace App\Actions\Season;

use App\Models\Land;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class ListSeasonsAction
{
    /**
     * @return Collection<int, Season>
     */
    public function execute(Land $land): Collection
    {
        return $land->seasons()->latest()->get();
    }
}
