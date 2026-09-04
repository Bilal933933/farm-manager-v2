<?php

namespace App\Http\Controllers\Api;

use App\Actions\PartyRole\CreatePartyRoleAction;
use App\Actions\PartyRole\DeletePartyRoleAction;
use App\Actions\PartyRole\ListPartyRolesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PartyRole\StorePartyRoleRequest;
use App\Http\Resources\PartyRoleResource;
use App\Models\Party;
use App\Models\PartyRole;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class PartyRoleController extends Controller
{
    use ExtractsRequestContext;

    private function ensurePartyTenant(Request $request, Party $party): void
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    public function index(Request $request, Party $party, ListPartyRolesAction $action)
    {
        $this->ensurePartyTenant($request, $party);

        return PartyRoleResource::collection($action->execute($party));
    }

    public function store(StorePartyRoleRequest $request, Party $party, CreatePartyRoleAction $action)
    {
        $this->ensurePartyTenant($request, $party);

        $role = $action->execute($party, $request->validated());

        return (new PartyRoleResource($role))->response()->setStatusCode(201);
    }

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
}
