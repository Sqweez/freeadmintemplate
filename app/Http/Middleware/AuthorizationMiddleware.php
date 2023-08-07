<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', null);
        \Log::info($header);
        if ($header && auth()->guest()) {
            $user = User::whereToken($header)->first();
            if ($user) {
                \Auth::login($user);
            }
        }
        return $next($request);
    }
}
