<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cost\CreateCostAction;
use App\Actions\Cost\DeleteCostAction;
use App\Actions\Cost\ListCostsAction;
use App\Actions\Cost\UpdateCostAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cost\StoreCostRequest;
use App\Http\Requests\Cost\UpdateCostRequest;
use App\Http\Resources\CostResource;
use App\Models\Cost;
use App\Models\Season;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class CostController extends Controller
{
    use ExtractsRequestContext;

    private function ensureSeasonTenant(Request $request, Season $season): void
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    private function ensureCostTenant(Request $request, Cost $cost): void
    {
        $cost->loadMissing('season.land');

        if ((string) $cost->season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    public function index(Request $request, Season $season, ListCostsAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        return CostResource::collection($action->execute($season));
    }

    public function store(StoreCostRequest $request, Season $season, CreateCostAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        $cost = $action->execute($season, $request->validated());

        return (new CostResource($cost))->response()->setStatusCode(201);
    }

    public function show(Request $request, Cost $cost)
    {
        $this->ensureCostTenant($request, $cost);

        return new CostResource($cost);
    }

    public function update(UpdateCostRequest $request, Cost $cost, UpdateCostAction $action)
    {
        $this->ensureCostTenant($request, $cost);

        $cost = $action->execute($cost, $request->validated());

        return new CostResource($cost);
    }

    public function destroy(Request $request, Cost $cost, DeleteCostAction $action)
    {
        $this->ensureCostTenant($request, $cost);

        if (! $this->hasPermission($request, 'delete_costs')) {
            abort(403);
        }

        $action->execute($cost);

        return response()->noContent();
    }
}
