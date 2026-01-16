<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware to redirect authenticated website users away from guest pages
 */
class WebsiteRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            // User is already authenticated, redirect to website home
            return redirect()->route('website.home')->with('info', 'أنت مسجل دخول بالفعل');
        }

        return $next($request);
    }
}

