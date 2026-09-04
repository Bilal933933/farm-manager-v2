<?php

namespace App\Actions\Cost;

use App\Models\Cost;

class DeleteCostAction
{
    public function execute(Cost $cost): void
    {
        $cost->delete();
    }
}
