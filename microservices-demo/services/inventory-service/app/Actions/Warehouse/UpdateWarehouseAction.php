<?php

namespace App\Actions\Warehouse;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class UpdateWarehouseAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Warehouse $warehouse, array $data): Warehouse
    {
        return DB::transaction(function () use ($warehouse, $data) {
            $warehouse->update($data);

            return $warehouse->refresh();
        });
    }
}
