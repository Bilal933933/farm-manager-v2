<?php

use App\Http\Controllers\Api\V1\ActivityLogController;
use App\Http\Controllers\Api\V1\PartyController;
use App\Http\Controllers\Api\V1\PartyRoleController;
use App\Http\Middleware\SetRequestContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function (Request $request) {
    return response()->json(['service' => 'parties', 'status' => 'ok']);
});

// API V1 Routes
Route::middleware(['verify.service.token', SetRequestContext::class])->prefix('v1')->group(function () {
    // Parties endpoints
    Route::apiResource('parties', PartyController::class);
    Route::delete('/parties/bulk/delete', [PartyController::class, 'bulkDelete'])->name('parties.bulk-delete');

    // Party Roles endpoints
    Route::get('/parties/{party}/roles', [PartyRoleController::class, 'index'])->name('party-roles.index');
    Route::post('/parties/{party}/roles', [PartyRoleController::class, 'store'])->name('party-roles.store');
    Route::delete('/parties/{party}/roles/{role}', [PartyRoleController::class, 'destroy'])->name('party-roles.destroy');

    // Activity Logs endpoints
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/parties/{partyId}/activity-logs', [ActivityLogController::class, 'party'])->name('activity-logs.party');
});

// Legacy API Routes (without version prefix for backwards compatibility)
Route::middleware(['verify.service.token', SetRequestContext::class])->group(function () {
    Route::apiResource('parties', PartyController::class);
    Route::get('/parties/{party}/roles', [PartyRoleController::class, 'index']);
    Route::post('/parties/{party}/roles', [PartyRoleController::class, 'store']);
    Route::delete('/parties/{party}/roles/{role}', [PartyRoleController::class, 'destroy']);
});
