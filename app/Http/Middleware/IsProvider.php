<?php

namespace App\Http\Middleware;

use App\Facades\Responder;
use Closure;
use Illuminate\Http\Request;

class IsProvider
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
        if (auth()->check() && auth()->user()->type === 'provider') {
            return $next($request);
        }
        
        return Responder::error(__('auth.unauthorized'), [], 401);
    }
}