<?php

namespace App\Actions\PartyRole;

use App\Models\PartyRole;
use Illuminate\Support\Facades\Log;

class DeletePartyRoleAction
{
    /**
     * Delete a party role.
     *
     *
     * @throws \Exception
     */
    public function execute(PartyRole $role): bool
    {
        try {
            $roleId = $role->id;
            $partyId = $role->party_id;

            $result = $role->delete();

            Log::info('Party role deleted', [
                'role_id' => $roleId,
                'party_id' => $partyId,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete party role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
