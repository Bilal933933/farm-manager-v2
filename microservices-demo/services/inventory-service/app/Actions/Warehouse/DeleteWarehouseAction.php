<?php

namespace App\Actions\Warehouse;

use App\Models\Warehouse;

class DeleteWarehouseAction
{
    public function execute(Warehouse $warehouse): void
    {
        $warehouse->delete();
    }
}
