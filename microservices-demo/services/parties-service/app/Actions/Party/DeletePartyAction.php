<?php

namespace App\Actions\Party;

use App\Models\Party;

class DeletePartyAction
{
    public function execute(Party $party): void
    {
        $party->delete();
    }
}
