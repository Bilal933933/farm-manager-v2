<?php

use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\PartyRoleController;
use App\Http\Middleware\SetRequestContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (Request $request) {
    return response()->json(['service' => 'parties', 'status' => 'ok']);
});

Route::middleware(['verify.service.token', SetRequestContext::class])->group(function () {
    Route::apiResource('parties', PartyController::class);
    Route::get('/parties/{party}/roles', [PartyRoleController::class, 'index']);
    Route::post('/parties/{party}/roles', [PartyRoleController::class, 'store']);
    Route::delete('/parties/{party}/roles/{role}', [PartyRoleController::class, 'destroy']);
});
