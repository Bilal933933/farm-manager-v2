<?php

namespace App\Http\Controllers\Api;

use App\Actions\Product\CreateProductAction;
use App\Actions\Product\DeleteProductAction;
use App\Actions\Product\ListProductsAction;
use App\Actions\Product\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ExtractsRequestContext;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ExtractsRequestContext;

    public function index(Request $request, ListProductsAction $action)
    {
        $products = $action->execute((string) $this->getCompanyId($request));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request, CreateProductAction $action)
    {
        $product = $action->execute($request->validated(), (string) $this->getCompanyId($request));

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    public function show(Request $request, Product $product)
    {
        if ((string) $product->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action)
    {
        if ((string) $product->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        $product = $action->execute($product, $request->validated());

        return new ProductResource($product);
    }

    public function destroy(Request $request, Product $product, DeleteProductAction $action)
    {
        if ((string) $product->company_id !== (string) $this->getCompanyId($request)) {
            abort(403);
        }

        if (! $this->hasPermission($request, 'delete_products')) {
            abort(403);
        }

        $action->execute($product);

        return response()->noContent();
    }
}
