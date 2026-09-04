<?php

namespace App\Http\Controllers\Api;

use App\Actions\Contract\CreateContractAction;
use App\Actions\Contract\DeleteContractAction;
use App\Actions\Contract\ListContractsAction;
use App\Actions\Contract\UpdateContractAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\StoreContractRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Land;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, Land $land, ListContractsAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return ContractResource::collection($action->execute($land));
    }

    public function store(StoreContractRequest $request, Land $land, CreateContractAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $contract = $action->execute($land, $request->validated());

        return (new ContractResource($contract))->response()->setStatusCode(201);
    }

    public function show(Request $request, Contract $contract)
    {
        $contract->loadMissing('land');

        if ((string) $contract->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new ContractResource($contract);
    }

    public function update(UpdateContractRequest $request, Contract $contract, UpdateContractAction $action)
    {
        $contract->loadMissing('land');

        if ((string) $contract->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $contract = $action->execute($contract, $request->validated());

        return new ContractResource($contract);
    }

    public function destroy(Request $request, Contract $contract, DeleteContractAction $action)
    {
        $contract->loadMissing('land');

        if ((string) $contract->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_contracts')) {
            abort(403);
        }

        $action->execute($contract);

        return response()->noContent();
    }
}
