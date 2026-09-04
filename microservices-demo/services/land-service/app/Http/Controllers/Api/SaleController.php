<?php

namespace App\Http\Controllers\Api;

use App\Actions\Sale\CreateSaleAction;
use App\Actions\Sale\DeleteSaleAction;
use App\Actions\Sale\ListSalesAction;
use App\Actions\Sale\UpdateSaleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Models\Season;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use ExtractsRequestContext;

    private function ensureSeasonTenant(Request $request, Season $season): void
    {
        $season->loadMissing('land');

        if ((string) $season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    private function ensureSaleTenant(Request $request, Sale $sale): void
    {
        $sale->loadMissing('season.land');

        if ((string) $sale->season->land->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }
    }

    public function index(Request $request, Season $season, ListSalesAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        return SaleResource::collection($action->execute($season));
    }

    public function store(StoreSaleRequest $request, Season $season, CreateSaleAction $action)
    {
        $this->ensureSeasonTenant($request, $season);

        $sale = $action->execute($season, $request->validated());

        return (new SaleResource($sale))->response()->setStatusCode(201);
    }

    public function show(Request $request, Sale $sale)
    {
        $this->ensureSaleTenant($request, $sale);

        return new SaleResource($sale);
    }

    public function update(UpdateSaleRequest $request, Sale $sale, UpdateSaleAction $action)
    {
        $this->ensureSaleTenant($request, $sale);

        $sale = $action->execute($sale, $request->validated());

        return new SaleResource($sale);
    }

    public function destroy(Request $request, Sale $sale, DeleteSaleAction $action)
    {
        $this->ensureSaleTenant($request, $sale);

        if (! $this->hasPermission($request, 'delete_sales')) {
            abort(403);
        }

        $action->execute($sale);

        return response()->noContent();
    }
}
