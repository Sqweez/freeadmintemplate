<?php

namespace App\Http\Middleware;

use App\Http\Controllers\api\BaseApiController;
use App\v2\Models\WholesaleClient;
use Closure;
use Illuminate\Http\Request;

class OptAuthMiddleware extends BaseApiController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if ($token && $user = WholesaleClient::whereAccessToken($token)->first()) {
            \Auth::login($user);
        }
        return $next($request);
        return $this->respondErrorNoReport('Данные авторизации устарели', 401);
    }
}
