<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkDeletePartiesAction
{
    /**
     * Bulk delete parties.
     *
     * @param  array<int, string>  $partyIds
     * @return array{deleted: int, failed: int}
     */
    public function execute(string $companyId, array $partyIds): array
    {
        try {
            return DB::transaction(function () use ($companyId, $partyIds) {
                $parties = Party::where('company_id', $companyId)
                    ->whereIn('id', $partyIds)
                    ->get();

                $deleted = 0;
                $failed = 0;

                foreach ($parties as $party) {
                    try {
                        $party->delete();
                        $deleted++;
                    } catch (\Exception $e) {
                        $failed++;
                        Log::error('Failed to delete party in bulk operation', [
                            'party_id' => $party->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                Log::info('Bulk delete completed', [
                    'company_id' => $companyId,
                    'deleted' => $deleted,
                    'failed' => $failed,
                ]);

                return ['deleted' => $deleted, 'failed' => $failed];
            });
        } catch (\Exception $e) {
            Log::error('Bulk delete operation failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
