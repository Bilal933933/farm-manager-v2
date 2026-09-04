<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\DB;

class CreatePartyAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, string $companyId): Party
    {
        return DB::transaction(function () use ($data, $companyId) {
            $party = Party::create([...$data, 'company_id' => $companyId]);

            return $party->refresh();
        });
    }
}
