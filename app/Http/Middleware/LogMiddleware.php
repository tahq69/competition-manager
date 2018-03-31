<?php namespace App\Http\Middleware;

/**
 * Class LogMiddleware
 * @package App\Http\Middleware
 */
class LogMiddleware
{
    public function handle($request, \Closure $next)
    {
        \Log::info('app.request', ['url' => $request->fullUrl()]);

        return $next($request);
    }
}
