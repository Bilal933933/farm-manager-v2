<?php

namespace App\Http\Controllers\Api;

use App\Actions\Land\CreateLandAction;
use App\Actions\Land\DeleteLandAction;
use App\Actions\Land\ListLandsAction;
use App\Actions\Land\UpdateLandAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Land\StoreLandRequest;
use App\Http\Requests\Land\UpdateLandRequest;
use App\Http\Resources\LandResource;
use App\Models\Land;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class LandController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, ListLandsAction $action)
    {
        $lands = $action->execute((string) $this->getCompanyId($request));

        return LandResource::collection($lands);
    }

    public function store(StoreLandRequest $request, CreateLandAction $action)
    {
        $land = $action->execute($request->validated(), (string) $this->getCompanyId($request));

        return (new LandResource($land))->response()->setStatusCode(201);
    }

    public function show(Request $request, Land $land)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new LandResource($land);
    }

    public function update(UpdateLandRequest $request, Land $land, UpdateLandAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $land = $action->execute($land, $request->validated());

        return new LandResource($land);
    }

    public function destroy(Request $request, Land $land, DeleteLandAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_lands')) {
            abort(403);
        }

        $action->execute($land);

        return response()->noContent();
    }
}
