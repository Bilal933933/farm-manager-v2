<?php

namespace App\Actions\Harvest;

use App\Models\Harvest;

class DeleteHarvestAction
{
    public function execute(Harvest $harvest): void
    {
        $harvest->delete();
    }
}
