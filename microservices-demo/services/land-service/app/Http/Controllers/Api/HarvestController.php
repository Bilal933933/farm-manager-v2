<?php

namespace App\Http\Controllers\Api;

use App\Actions\Harvest\CreateHarvestAction;
use App\Actions\Harvest\DeleteHarvestAction;
use App\Actions\Harvest\ListHarvestsAction;
use App\Actions\Harvest\UpdateHarvestAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Harvest\StoreHarvestRequest;
use App\Http\Requests\Harvest\UpdateHarvestRequest;
use App\Http\Resources\HarvestResource;
use App\Models\Harvest;
use App\Models\Season;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    use ExtractsRequestContext;

    private function ensureSeasonTenant(Request $request, Season $season): void
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    private function ensureHarvestTenant(Request $request, Harvest $harvest): void
    {
        $harvest->loadMissing('season.land');

        if ((string) $harvest->season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    public function index(Request $request, Season $season, ListHarvestsAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        return HarvestResource::collection($action->execute($season));
    }

    public function store(StoreHarvestRequest $request, Season $season, CreateHarvestAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        $harvest = $action->execute($season, $request->validated());

        return (new HarvestResource($harvest))->response()->setStatusCode(201);
    }

    public function show(Request $request, Harvest $harvest)
    {
        $this->ensureHarvestTenant($request, $harvest);

        return new HarvestResource($harvest);
    }

    public function update(UpdateHarvestRequest $request, Harvest $harvest, UpdateHarvestAction $action)
    {
        $this->ensureHarvestTenant($request, $harvest);

        $harvest = $action->execute($harvest, $request->validated());

        return new HarvestResource($harvest);
    }

    public function destroy(Request $request, Harvest $harvest, DeleteHarvestAction $action)
    {
        $this->ensureHarvestTenant($request, $harvest);

        if (! $this->hasPermission($request, 'delete_harvests')) {
            abort(403);
        }

        $action->execute($harvest);

        return response()->noContent();
    }
}
