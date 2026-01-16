<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalSanctumMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware allows optional authentication - if a token is provided
     * and valid, the user will be authenticated. If no token or invalid token
     * is provided, the request continues without authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only attempt authentication if a bearer token is present
        if ($request->bearerToken()) {
            try {
                // Attempt to get the user from the sanctum guard
                $user = Auth::guard('sanctum')->user();

                // Only set the user if a valid user is found
                if ($user) {
                    Auth::setUser($user);
                }
                // If user is null (invalid/expired token), we simply continue
                // without authentication rather than throwing an error
            } catch (\Exception) {
                // If any exception occurs during token validation,
                // continue without authentication
                // This ensures the middleware remains "optional"
            }
        }

        return $next($request);
    }
}
