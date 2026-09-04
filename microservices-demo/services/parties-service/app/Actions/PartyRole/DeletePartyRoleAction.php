<?php

namespace App\Actions\PartyRole;

use App\Models\PartyRole;

class DeletePartyRoleAction
{
    public function execute(PartyRole $role): void
    {
        $role->delete();
    }
}
