<?php

use App\Http\Controllers\Api\LandController;
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
});
