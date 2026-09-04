<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Middleware\SetRequestContext;
use Illuminate\Support\Facades\Route;

Route::middleware(['verify.service.token', SetRequestContext::class])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('warehouses', WarehouseController::class);
});
