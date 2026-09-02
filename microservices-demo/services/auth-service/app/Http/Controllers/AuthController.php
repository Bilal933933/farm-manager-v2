<?php

namespace App\Http\Controllers;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $action)
    {
        $resource = $action->execute($request->validated());

        Auth::login($resource->resource['user']);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $resource->response()->setStatusCode(201);
    }

    public function login(LoginRequest $request, LoginAction $action)
    {
        $resource = $action->execute($request->input('email'), $request->input('password'));

        Auth::login($resource->resource['user']);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $resource->response();
    }
}
