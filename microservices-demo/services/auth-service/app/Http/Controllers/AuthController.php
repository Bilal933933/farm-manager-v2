<?php

namespace App\Http\Controllers;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $registerAction)
    {
        $result = $registerAction->execute($request->validated());

        // تسجيل دخول تلقائي بعد التسجيل
        Auth::login($result['user']);

        // regenerate session للـ SPA
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'company' => [
                    'id' => $result['company']->id,
                    'name' => $result['company']->name,
                    'slug' => $result['company']->slug,
                ],
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                ],
                'role' => 'مدير',
            ],
        ], 201);
    }

    public function login(LoginRequest $request, LoginAction $action)
    {
        $user = $action->execute($request->input('email'), $request->input('password'));

        Auth::login($user);

        // regenerate session للـ SPA
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_id' => $user->company_id,
                ],
                'company' => $user->company ? [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'plan' => $user->company->plan,
                ] : null,
                'role' => $user->role?->name,
            ],
        ]);
    }
}
