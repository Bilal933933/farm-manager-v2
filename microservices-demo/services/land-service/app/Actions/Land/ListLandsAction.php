<?php

namespace App\Actions\Land;

use App\Models\Land;
use Illuminate\Database\Eloquent\Collection;

class ListLandsAction
{
    /**
     * @return Collection<int, Land>
     */
    public function execute(string $companyId): Collection
    {
        return Land::where('company_id', $companyId)->latest()->get();
    }
}
