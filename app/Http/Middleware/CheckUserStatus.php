<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        if (in_array($user->status, ['suspended', 'banned'])) {
            return response()->json([
                'status' => 403,
                'message' => __('auth.account_inactive')
            ], 403);
        }

        return $next($request);
    }
}
