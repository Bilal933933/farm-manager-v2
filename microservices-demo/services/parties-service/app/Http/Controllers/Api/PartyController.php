<?php

namespace App\Http\Controllers\Api;

use App\Actions\Party\CreatePartyAction;
use App\Actions\Party\DeletePartyAction;
use App\Actions\Party\ListPartiesAction;
use App\Actions\Party\UpdatePartyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Party\StorePartyRequest;
use App\Http\Requests\Party\UpdatePartyRequest;
use App\Http\Resources\PartyResource;
use App\Models\Party;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, ListPartiesAction $action)
    {
        $parties = $action->execute((string) $this->getCompanyId($request));

        return PartyResource::collection($parties);
    }

    public function store(StorePartyRequest $request, CreatePartyAction $action)
    {
        $party = $action->execute($request->validated(), (string) $this->getCompanyId($request));

        return (new PartyResource($party))->response()->setStatusCode(201);
    }

    public function show(Request $request, Party $party)
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new PartyResource($party->loadMissing('roles'));
    }

    public function update(UpdatePartyRequest $request, Party $party, UpdatePartyAction $action)
    {
        if ((string) $party->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $party = $action->execute($party, $request->validated());

        return new PartyResource($party);
    }

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
}
