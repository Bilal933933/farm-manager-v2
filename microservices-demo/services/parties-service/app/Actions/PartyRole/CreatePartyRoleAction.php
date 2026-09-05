<?php

namespace App\Actions\PartyRole;

use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePartyRoleAction
{
    /**
     * Create a new party role.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Exception
     */
    public function execute(Party $party, array $data): PartyRole
    {
        try {
            return DB::transaction(function () use ($party, $data) {
                $role = $party->roles()->create($data);

                Log::info('Party role created', [
                    'role_id' => $role->id,
                    'party_id' => $party->id,
                    'role_type' => $role->role->value,
                ]);

                return $role->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to create party role', [
                'party_id' => $party->id,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }
}
