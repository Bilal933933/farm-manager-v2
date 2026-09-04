<?php

namespace App\Actions\Contract;

use App\Models\Contract;
use App\Models\Land;
use Illuminate\Database\Eloquent\Collection;

class ListContractsAction
{
    /**
     * @return Collection<int, Contract>
     */
    public function execute(Land $land): Collection
    {
        return $land->contracts()->latest()->get();
    }
}
