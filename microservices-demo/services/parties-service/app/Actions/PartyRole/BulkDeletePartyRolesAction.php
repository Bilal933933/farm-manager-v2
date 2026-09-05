<?php

namespace App\Actions\PartyRole;

use App\Models\Party;
use App\Models\PartyRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkDeletePartyRolesAction
{
    /**
     * Bulk delete party roles for a specific party.
     *
     * @param  array<int, string>  $roleIds
     * @return array{deleted: int, failed: int}
     */
    public function execute(Party $party, array $roleIds): array
    {
        try {
            return DB::transaction(function () use ($party, $roleIds) {
                $roles = PartyRole::where('party_id', $party->id)
                    ->whereIn('id', $roleIds)
                    ->get();

                $deleted = 0;
                $failed = 0;

                foreach ($roles as $role) {
                    try {
                        $role->delete();
                        $deleted++;
                    } catch (\Exception $e) {
                        $failed++;
                        Log::error('Failed to delete party role in bulk operation', [
                            'role_id' => $role->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Log::info('Bulk delete party roles completed', [
                    'party_id' => $party->id,
                    'deleted' => $deleted,
                    'failed' => $failed,
                ]);

                return ['deleted' => $deleted, 'failed' => $failed];
            });
        } catch (\Exception $e) {
            Log::error('Bulk delete party roles operation failed', [
                'party_id' => $party->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
