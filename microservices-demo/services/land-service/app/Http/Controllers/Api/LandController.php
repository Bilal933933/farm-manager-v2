<?php

namespace App\Http\Controllers\Api;

use App\Actions\Land\CreateLandAction;
use App\Actions\Land\ListLandsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Land\StoreLandRequest;
use App\Http\Resources\LandResource;
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
}
