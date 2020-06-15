<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

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
        if (!$request->hasHeader('authorization')) {
            return response()->json(['error' => 'You must get API KEY from your provider']);
        }
        $authToken = $request->header('authorization');
        $user = User::where('token', $authToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Access denied']);
        }

        return $next($request);
    }
}
