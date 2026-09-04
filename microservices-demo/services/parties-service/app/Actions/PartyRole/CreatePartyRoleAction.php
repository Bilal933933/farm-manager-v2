<?php

namespace App\Actions\PartyRole;

use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Support\Facades\DB;

class CreatePartyRoleAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Party $party, array $data): PartyRole
    {
        return DB::transaction(function () use ($party, $data) {
            $role = $party->roles()->create($data);

            return $role->refresh();
        });
    }
}
