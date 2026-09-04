<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetRequestContext
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('user_id', $request->header('X-User-Id'));
        $request->attributes->set('company_id', $request->header('X-Company-Id'));

        $perms = $request->header('X-Permissions', '');
        $request->attributes->set('permissions', $perms ? explode(',', $perms) : []);

        return $next($request);
    }
}
