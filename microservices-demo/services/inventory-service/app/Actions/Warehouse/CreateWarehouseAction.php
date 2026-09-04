<?php

namespace App\Actions\Warehouse;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class CreateWarehouseAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, string $companyId): Warehouse
    {
        return DB::transaction(function () use ($data, $companyId) {
            $warehouse = Warehouse::create([...$data, 'company_id' => $companyId]);

            return $warehouse->refresh();
        });
    }
}
