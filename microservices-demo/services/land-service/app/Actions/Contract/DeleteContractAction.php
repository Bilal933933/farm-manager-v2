<?php

namespace App\Actions\Contract;

use App\Models\Contract;

class DeleteContractAction
{
    public function execute(Contract $contract): void
    {
        $contract->delete();
    }
}
