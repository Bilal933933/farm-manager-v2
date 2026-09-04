<?php

namespace App\Http\Controllers\Api;

use App\Actions\Season\CreateSeasonAction;
use App\Actions\Season\DeleteSeasonAction;
use App\Actions\Season\ListSeasonsAction;
use App\Actions\Season\UpdateSeasonAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Season\StoreSeasonRequest;
use App\Http\Requests\Season\UpdateSeasonRequest;
use App\Http\Resources\SeasonResource;
use App\Models\Land;
use App\Models\Season;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, Land $land, ListSeasonsAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return SeasonResource::collection($action->execute($land));
    }

    public function store(StoreSeasonRequest $request, Land $land, CreateSeasonAction $action)
    {
        if ((string) $land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $season = $action->execute($land, $request->validated());

        return (new SeasonResource($season))->response()->setStatusCode(201);
    }

    public function show(Request $request, Season $season)
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new SeasonResource($season);
    }

    public function update(UpdateSeasonRequest $request, Season $season, UpdateSeasonAction $action)
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $season = $action->execute($season, $request->validated());

        return new SeasonResource($season);
    }

    public function destroy(Request $request, Season $season, DeleteSeasonAction $action)
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_seasons')) {
            abort(403);
        }

        $action->execute($season);

        return response()->noContent();
    }
}
