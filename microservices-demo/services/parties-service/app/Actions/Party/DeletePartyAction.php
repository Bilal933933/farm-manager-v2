<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\Log;

class DeletePartyAction
{
    /**
     * Delete a party (soft delete).
     *
     *
     * @throws \Exception
     */
    public function execute(Party $party): bool
    {
        try {
            $partyId = $party->id;
            $companyId = $party->company_id;

            $result = $party->delete();

            Log::info('Party deleted successfully', [
                'party_id' => $partyId,
                'company_id' => $companyId,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete party', [
                'party_id' => $party->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
