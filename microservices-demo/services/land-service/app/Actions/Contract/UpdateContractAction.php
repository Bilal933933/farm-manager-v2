<?php

namespace App\Actions\Contract;

use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class UpdateContractAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            $contract->update($data);

            return $contract->refresh();
        });
    }
}
