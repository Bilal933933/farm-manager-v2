<?php

namespace App\Http\Controllers\Api;

use App\Actions\Crop\CreateCropAction;
use App\Actions\Crop\DeleteCropAction;
use App\Actions\Crop\ListCropsAction;
use App\Actions\Crop\UpdateCropAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crop\StoreCropRequest;
use App\Http\Requests\Crop\UpdateCropRequest;
use App\Http\Resources\CropResource;
use App\Models\Crop;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class CropController extends Controller
{
    use ExtractsRequestContext;

    public function index(ListCropsAction $action)
    {
        return CropResource::collection($action->execute());
    }

    public function store(StoreCropRequest $request, CreateCropAction $action)
    {
        $crop = $action->execute($request->validated());

        return (new CropResource($crop))->response()->setStatusCode(201);
    }

    public function show(Crop $crop)
    {
        return new CropResource($crop);
    }

    public function update(UpdateCropRequest $request, Crop $crop, UpdateCropAction $action)
    {
        $crop = $action->execute($crop, $request->validated());

        return new CropResource($crop);
    }

    public function destroy(Request $request, Crop $crop, DeleteCropAction $action)
    {
        if (! $this->hasPermission($request, 'delete_crops')) {
            abort(403);
        }

        $action->execute($crop);

        return response()->noContent();
    }
}
