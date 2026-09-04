<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;

class ListPartiesAction
{
    /**
     * @return Collection<int, Party>
     */
    public function execute(string $companyId): Collection
    {
        return Party::where('company_id', $companyId)->latest()->get();
    }
}
