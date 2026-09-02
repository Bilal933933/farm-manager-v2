<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth')->post('/auth/logout', [AuthController::class, 'logout']);
Route::middleware('auth')->get('/auth/me', [AuthController::class, 'me']);
Route::middleware('auth')->get('/auth/verify', [AuthController::class, 'verify']);
