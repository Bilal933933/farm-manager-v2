<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\DB;

class UpdatePartyAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Party $party, array $data): Party
    {
        return DB::transaction(function () use ($party, $data) {
            $party->update($data);

            return $party->refresh();
        });
    }
}
