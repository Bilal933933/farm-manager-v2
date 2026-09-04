<?php

namespace App\Actions\Warehouse;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;

class ListWarehousesAction
{
    /**
     * @return Collection<int, Warehouse>
     */
    public function execute(string $companyId): Collection
    {
        return Warehouse::where('company_id', $companyId)->latest()->get();
    }
}
