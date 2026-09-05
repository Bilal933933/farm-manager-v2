<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Party\BulkDeletePartiesAction;
use App\Actions\Party\CreatePartyAction;
use App\Actions\Party\DeletePartyAction;
use App\Actions\Party\SearchPartiesAction;
use App\Actions\Party\UpdatePartyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Party\StorePartyRequest;
use App\Http\Requests\Party\UpdatePartyRequest;
use App\Http\Resources\V1\PartyCollection;
use App\Http\Resources\V1\PartyResource;
use App\Models\Party;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    use ExtractsRequestContext;

    /**
     * List all parties for the company.
     */
    public function index(Request $request, SearchPartiesAction $action)
    {
        $filters = $request->only(['search', 'status', 'role', 'sort_by', 'sort_order', 'per_page']);
        $parties = $action->execute((string) $this->getCompanyId($request), $filters);

        return new PartyCollection($parties);
    }

    /**
     * Create a new party.
     */
    public function store(StorePartyRequest $request, CreatePartyAction $action)
    {
        $party = $action->execute($request->validated(), (string) $this->getCompanyId($request));

        return (new PartyResource($party))->response()->setStatusCode(201);
    }

    /**
     * Get a specific party.
     */
    public function show(Request $request, Party $party)
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new PartyResource($party->loadMissing('roles'));
    }

    /**
     * Update a party.
     */
    public function update(UpdatePartyRequest $request, Party $party, UpdatePartyAction $action)
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $party = $action->execute($party, $request->validated());

        return new PartyResource($party);
    }

    /**
     * Delete a party.
     */
    public function destroy(Request $request, Party $party, DeletePartyAction $action)
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_parties')) {
            abort(403);
        }

        $action->execute($party);

        return response()->noContent();
    }

    /**
     * Bulk delete parties.
     */
    public function bulkDelete(Request $request, BulkDeletePartiesAction $action)
    {
        if (! $this->hasPermission($request, 'delete_parties')) {
            abort(403);
        }

        $partyIds = $request->input('party_ids', []);

        if (count($partyIds) > 100) {
            return response()->json([
                'message' => 'Maximum 100 parties can be deleted at once',
            ], 422);
        }

        $result = $action->execute((string) $this->getCompanyId($request), $partyIds);

        return response()->json([
            'message' => 'Bulk delete completed',
            'data' => $result,
        ]);
    }
}
