<?php

namespace App\Actions\Party;

use App\Models\Party;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePartyAction
{
    /**
     * Create a new party.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Exception
     */
    public function execute(array $data, string $companyId): Party
    {
        try {
            return DB::transaction(function () use ($data, $companyId) {
                $party = Party::create([...$data, 'company_id' => $companyId]);

                Log::info('Party created successfully', [
                    'party_id' => $party->id,
                    'company_id' => $companyId,
                    'name' => $party->name,
                ]);

                return $party->refresh();
            });
        } catch (\Exception $e) {
            Log::error('Failed to create party', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }
}
