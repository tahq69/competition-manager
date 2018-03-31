<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class RegisterUserStorageFolder
 * @package App\Http\Middleware
 */
class RegisterUserStorageFolder
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            return redirect('/login');
        }

        if (!Auth::user()->isSuperAdmin()) {
            // For users who is not in group of administrators set their own
            // folder for manager and make impossible to see/change files of
            // other users.
            Config::set('cripfilesys.user_folder', Auth::user()->slug());
        }

        return $next($request);
    }
}
