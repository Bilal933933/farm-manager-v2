<?php

namespace App\Observers;

use App\Enums\PartyStatus;
use App\Models\Party;
use Illuminate\Support\Facades\Log;

class PartyObserver
{
    /**
     * Handle the Party "creating" event.
     */
    public function creating(Party $party): void
    {
        // Ensure status is set if not provided
        if (! $party->status) {
            $party->status = PartyStatus::Active;
        }
    }

    /**
     * Handle the Party "created" event.
     */
    public function created(Party $party): void
    {
        Log::info('Party created', [
            'party_id' => $party->id,
            'company_id' => $party->company_id,
            'name' => $party->name,
        ]);
    }

    /**
     * Handle the Party "updated" event.
     */
    public function updated(Party $party): void
    {
        Log::info('Party updated', [
            'party_id' => $party->id,
            'company_id' => $party->company_id,
            'changes' => $party->getChanges(),
        ]);
    }

    /**
     * Handle the Party "deleted" event.
     */
    public function deleted(Party $party): void
    {
        Log::info('Party deleted', [
            'party_id' => $party->id,
            'company_id' => $party->company_id,
            'soft_deleted' => $party->trashed(),
        ]);
    }

    /**
     * Handle the Party "restored" event.
     */
    public function restored(Party $party): void
    {
        Log::info('Party restored', [
            'party_id' => $party->id,
            'company_id' => $party->company_id,
        ]);
    }

    /**
     * Handle the Party "force deleted" event.
     */
    public function forceDeleted(Party $party): void
    {
        Log::warning('Party force deleted', [
            'party_id' => $party->id,
            'company_id' => $party->company_id,
        ]);
    }
}
