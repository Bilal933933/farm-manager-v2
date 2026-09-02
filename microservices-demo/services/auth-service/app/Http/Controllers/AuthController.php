<?php

namespace App\Http\Controllers;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\MeAction;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\VerifyAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\MeResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VerifyResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $action)
    {
        $result = $action->execute($request->validated());

        return response()->json(['success' => true, 'data' => [
            'company' => new CompanyResource($result['company']),
            'user' => new UserResource($result['user']),
            'role' => $result['role']->name,
            'token' => $result['token'],
        ]], 201);
    }

    public function login(LoginRequest $request, LoginAction $action)
    {
        $result = $action->execute($request->input('email'), $request->input('password'));

        return response()->json(['success' => true, 'data' => [
            'user' => new UserResource($result['user']),
            'company' => $result['user']->company ? new CompanyResource($result['user']->company) : null,
            'role' => $result['user']->role?->name,
            'token' => $result['token'],
        ]]);
    }

    public function logout(Request $request, LogoutAction $action)
    {
        $action->execute($request);

        return response()->json(['success' => true, 'message' => 'تم تسجيل الخروج']);
    }

    public function me(Request $request, MeAction $action)
    {
        $user = $action->execute($request->user());

        return (new MeResource($user))->response();
    }

    public function verify(Request $request, VerifyAction $action)
    {
        $request->validate(['token' => 'required|string']);

        $user = $action->execute($request->input('token'));

        if (! $user) {
            return response()->json(['valid' => false], 401);
        }

        return new VerifyResource($user);
    }
}
