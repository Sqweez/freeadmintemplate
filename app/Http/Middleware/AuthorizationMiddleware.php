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
        $header = $request->header('Authorization', null);
        if ($header && auth()->guest()) {
            $user = User::whereToken($header)->first();
            if ($user) {
                \Auth::login($user);
            }
        }
        return $next($request);
    }
}
