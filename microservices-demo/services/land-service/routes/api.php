<?php

use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\CostController;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\HarvestController;
use App\Http\Controllers\Api\LandController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Middleware\SetRequestContext;
use Illuminate\Support\Facades\Route;

Route::middleware(['verify.service.token', SetRequestContext::class])->group(function () {
    Route::get('/lands', [LandController::class, 'index']);
    Route::post('/lands', [LandController::class, 'store']);
    Route::get('/lands/{land}', [LandController::class, 'show']);
    Route::put('/lands/{land}', [LandController::class, 'update']);
    Route::delete('/lands/{land}', [LandController::class, 'destroy']);

    Route::get('/lands/{land}/seasons', [SeasonController::class, 'index']);
    Route::post('/lands/{land}/seasons', [SeasonController::class, 'store']);
    Route::get('/seasons/{season}', [SeasonController::class, 'show']);
    Route::put('/seasons/{season}', [SeasonController::class, 'update']);
    Route::delete('/seasons/{season}', [SeasonController::class, 'destroy']);

    Route::get('/lands/{land}/contracts', [ContractController::class, 'index']);
    Route::post('/lands/{land}/contracts', [ContractController::class, 'store']);
    Route::get('/contracts/{contract}', [ContractController::class, 'show']);
    Route::put('/contracts/{contract}', [ContractController::class, 'update']);
    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy']);

    Route::get('/seasons/{season}/costs', [CostController::class, 'index']);
    Route::post('/seasons/{season}/costs', [CostController::class, 'store']);
    Route::get('/costs/{cost}', [CostController::class, 'show']);
    Route::put('/costs/{cost}', [CostController::class, 'update']);
    Route::delete('/costs/{cost}', [CostController::class, 'destroy']);

    Route::get('/seasons/{season}/harvests', [HarvestController::class, 'index']);
    Route::post('/seasons/{season}/harvests', [HarvestController::class, 'store']);
    Route::get('/harvests/{harvest}', [HarvestController::class, 'show']);
    Route::put('/harvests/{harvest}', [HarvestController::class, 'update']);
    Route::delete('/harvests/{harvest}', [HarvestController::class, 'destroy']);

    Route::get('/seasons/{season}/sales', [SaleController::class, 'index']);
    Route::post('/seasons/{season}/sales', [SaleController::class, 'store']);
    Route::get('/sales/{sale}', [SaleController::class, 'show']);
    Route::put('/sales/{sale}', [SaleController::class, 'update']);
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy']);

    Route::get('/crops', [CropController::class, 'index']);
    Route::post('/crops', [CropController::class, 'store']);
    Route::get('/crops/{crop}', [CropController::class, 'show']);
    Route::put('/crops/{crop}', [CropController::class, 'update']);
    Route::delete('/crops/{crop}', [CropController::class, 'destroy']);
});
