<?php

namespace App\Http\Middleware;

use App\Models\FitUser;
use Closure;

class FitnessAuthorizationMiddleware
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
        $header =  $request->get('token', $request->header('Authorization', null));
        $user = null;
        if ($header) {
            $user = FitUser::whereToken($header)->first();
            if ($user) {
                \Auth::login($user);
            }
        }
        if (is_null($user)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        return $next($request);
    }
}
