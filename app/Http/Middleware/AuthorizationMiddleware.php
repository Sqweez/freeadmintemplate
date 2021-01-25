<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use http\Client\Response;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        $authToken = $request->header('Authorization');
        $user = User::where('token', $authToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
