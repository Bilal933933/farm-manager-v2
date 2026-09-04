<?php

namespace App\Http\Controllers\Api;

use App\Actions\Warehouse\CreateWarehouseAction;
use App\Actions\Warehouse\DeleteWarehouseAction;
use App\Actions\Warehouse\ListWarehousesAction;
use App\Actions\Warehouse\UpdateWarehouseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, ListWarehousesAction $action)
    {
        $warehouses = $action->execute((string) $this->getCompanyId($request));

        return WarehouseResource::collection($warehouses);
    }

    public function store(StoreWarehouseRequest $request, CreateWarehouseAction $action)
    {
        $warehouse = $action->execute($request->validated(), (string) $this->getCompanyId($request));

        return (new WarehouseResource($warehouse))->response()->setStatusCode(201);
    }

    public function show(Request $request, Warehouse $warehouse)
    {
        if ((string) $warehouse->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new WarehouseResource($warehouse);
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse, UpdateWarehouseAction $action)
    {
        if ((string) $warehouse->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $warehouse = $action->execute($warehouse, $request->validated());

        return new WarehouseResource($warehouse);
    }

    public function destroy(Request $request, Warehouse $warehouse, DeleteWarehouseAction $action)
    {
        if ((string) $warehouse->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_warehouses')) {
            abort(403);
        }

        $action->execute($warehouse);

        return response()->noContent();
    }
}
