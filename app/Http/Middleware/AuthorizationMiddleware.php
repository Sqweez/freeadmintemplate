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
    {        return $next($request);


        if (!$request->hasHeader('authorization')) {
            return response()->json(['error' => 'Access denied'], 404);
        }
        $authToken = $request->header('authorization');
        $user = User::where('token', $authToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Access denied']);
        }

        return $next($request);
    }
}
