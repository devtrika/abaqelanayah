<?php

namespace App\Http\Middleware;

use App\Facades\Responder;
use Closure;
use Illuminate\Http\Request;

class IsApprovedProvider
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
        // Check if user is authenticated
        if (!auth()->check()) {
            return Responder::error(__('auth.unauthorized'), [], 401);
        }

        $user = auth()->user();
        
        // Check if user is a provider
        if ($user->type !== 'provider') {
            return Responder::error(__('auth.unauthorized'), [], 403);
        }
        
        // Load provider relationship if not already loaded
        if (!$user->relationLoaded('provider')) {
            $user->load('provider');
        }
        
        // Check if provider exists and is approved
        if (!$user->provider || $user->provider->status !== 'accepted') {
            return Responder::error(__('auth.not_approved'), [], 403);
        }
        
        return $next($request);
    }
}