<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePartyAction
{
    /**
     * Update an existing party.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Exception
     */
    public function execute(Party $party, array $data): Party
    {
        try {
            return DB::transaction(function () use ($party, $data) {
                $oldData = $party->only(['name', 'phone', 'email', 'status']);

                $party->update($data);

                Log::info('Party updated successfully', [
                    'party_id' => $party->id,
                    'company_id' => $party->company_id,
                    'old_data' => $oldData,
                    'new_data' => $data,
                ]);

                return $party->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to update party', [
                'party_id' => $party->id,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }
}
