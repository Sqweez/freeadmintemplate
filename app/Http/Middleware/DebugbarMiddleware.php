<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class DebugbarMiddleware
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
        $response = $next($request);

        if (!config('debugbar.enabled')) {
            return $response;
        }

        if (
            $response instanceof JsonResponse &&
            app()->bound('debugbar') &&
            app('debugbar')->isEnabled() &&
            is_object($response->getData())
        ) {
            $response->setData($response->getData(true) + [
                    '_debugbar' => app('debugbar')->getData(),
                ]);
        }

        return $response;
    }
}
