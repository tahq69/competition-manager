<?php namespace App\Http\Middleware;

use Closure;

/**
 * Class CORS
 * @package App\Http\Middleware
 */
class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            "Access-Control-Allow-Origin"   => "*",
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization, X-Requested-With, token'
        ];

        if ($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return \Response::make('OK', 200, $headers);
        }
        $response = $next($request);
        foreach ($headers as $key => $value)
            $response->headers->set($key, $value);

        return $response;
    }
}