<?php

namespace App\Actions\PartyRole;

use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Database\Eloquent\Collection;

class ListPartyRolesAction
{
    /**
     * @return Collection<int, PartyRole>
     */
    public function execute(Party $party): Collection
    {
        return $party->roles()->latest()->get();
    }
}
