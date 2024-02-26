<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Concerns\ReturnsJsonResponse;
use Closure;

class ExceptionHandlingMiddleware
{
    use ReturnsJsonResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $exception) {
            return $this->responseException($exception);
        }
    }
}
