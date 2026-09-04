<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyServiceToken
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Service-Token');

        if (! $token || $token !== config('app.service_token')) {
            return response()->json(['message' => 'Unauthorized service'], 401);
        }

        return $next($request);
    }
}
