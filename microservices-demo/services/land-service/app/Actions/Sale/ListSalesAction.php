<?php

namespace App\Actions\Sale;

use App\Models\Sale;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

class ListSalesAction
{
    /**
     * @return Collection<int, Sale>
     */
    public function execute(Season $season): Collection
    {
        return $season->sales()->latest()->get();
    }
}
