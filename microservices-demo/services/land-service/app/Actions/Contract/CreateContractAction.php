<?php

namespace App\Actions\Contract;

use App\Models\Contract;
use App\Models\Land;
use Illuminate\Support\Facades\DB;

class CreateContractAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Land $land, array $data): Contract
    {
        return DB::transaction(function () use ($land, $data) {
            $contract = Contract::create([...$data, 'land_id' => $land->id]);

            return $contract->refresh();
        });
    }
}
