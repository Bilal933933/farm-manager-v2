<?php

namespace App\Actions\Crop;

use App\Models\Crop;
use Illuminate\Database\Eloquent\Collection;

class ListCropsAction
{
    /**
     * @return Collection<int, Crop>
     */
    public function execute(): Collection
    {
        return Crop::latest()->get();
    }
}
