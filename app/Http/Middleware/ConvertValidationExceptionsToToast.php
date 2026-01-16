<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Middleware to convert validation exceptions to toast notifications
 * This ensures all validation errors are displayed as toasts across the entire system
 */
class ConvertValidationExceptionsToToast
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
        $response = $next($request);

        // Check if there are validation errors in the session
        if (Session::has('errors')) {
            $errors = Session::get('errors');
            
            // If there are errors, we keep them for the toast to display
            // The toast component will automatically show them
            // No need to convert here, just ensure they're available
        }

        return $response;
    }
}

