<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PartyRole\CreatePartyRoleAction;
use App\Actions\PartyRole\DeletePartyRoleAction;
use App\Actions\PartyRole\ListPartyRolesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyRole\StorePartyRoleRequest;
use App\Http\Resources\V1\PartyRoleResource;
use App\Models\Party;
use App\Models\PartyRole;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class PartyRoleController extends Controller
{
    use ExtractsRequestContext;

    /**
     * List roles for a party.
     */
    public function index(Request $request, Party $party, ListPartyRolesAction $action)
    {
        $this->ensurePartyTenant($request, $party);

        $roles = $action->execute($party);

        return PartyRoleResource::collection($roles);
    }

    /**
     * Create a new role for a party.
     */
    public function store(StorePartyRoleRequest $request, Party $party, CreatePartyRoleAction $action)
    {
        $this->ensurePartyTenant($request, $party);

        $role = $action->execute($party, $request->validated());

        return (new PartyRoleResource($role))->response()->setStatusCode(201);
    }

    /**
     * Delete a role from a party.
     */
    public function destroy(Request $request, Party $party, PartyRole $role, DeletePartyRoleAction $action)
    {
        $this->ensurePartyTenant($request, $party);

        if ((string) $role->party_id !== (string) $party->id) {
            abort(404);
        }

        if (! $this->hasPermission($request, 'delete_parties')) {
            abort(403);
        }

        $action->execute($role);

        return response()->noContent();
    }

    /**
     * Ensure party belongs to requesting company.
     */
    private function ensurePartyTenant(Request $request, Party $party): void
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }
}
