<?php

namespace App\Actions\Land;

use App\Models\Land;

class DeleteLandAction
{
    public function execute(Land $land): void
    {
        $land->delete();
    }
}
