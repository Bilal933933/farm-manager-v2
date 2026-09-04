<?php

namespace App\Actions\Sale;

use App\Models\Sale;

class DeleteSaleAction
{
    public function execute(Sale $sale): void
    {
        $sale->delete();
    }
}
