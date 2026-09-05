<?php

namespace App\Observers;

use App\Models\PartyRole;
use Illuminate\Support\Facades\Log;

class PartyRoleObserver
{
    /**
     * Handle the PartyRole "created" event.
     */
    public function created(PartyRole $partyRole): void
    {
        Log::info('Party role created', [
            'role_id' => $partyRole->id,
            'party_id' => $partyRole->party_id,
            'role' => $partyRole->role->value,
        ]);
    }

    /**
     * Handle the PartyRole "updated" event.
     */
    public function updated(PartyRole $partyRole): void
    {
        Log::info('Party role updated', [
            'role_id' => $partyRole->id,
            'party_id' => $partyRole->party_id,
            'changes' => $partyRole->getChanges(),
        ]);
    }

    /**
     * Handle the PartyRole "deleted" event.
     */
    public function deleted(PartyRole $partyRole): void
    {
        Log::info('Party role deleted', [
            'role_id' => $partyRole->id,
            'party_id' => $partyRole->party_id,
            'role' => $partyRole->role->value,
            'soft_deleted' => $partyRole->trashed(),
        ]);
    }

    /**
     * Handle the PartyRole "restored" event.
     */
    public function restored(PartyRole $partyRole): void
    {
        Log::info('Party role restored', [
            'role_id' => $partyRole->id,
            'party_id' => $partyRole->party_id,
            'role' => $partyRole->role->value,
        ]);
    }

    /**
     * Handle the PartyRole "force deleted" event.
     */
    public function forceDeleted(PartyRole $partyRole): void
    {
        Log::warning('Party role force deleted', [
            'role_id' => $partyRole->id,
            'party_id' => $partyRole->party_id,
            'role' => $partyRole->role->value,
        ]);
    }
}
