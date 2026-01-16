<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\Responder;

class CheckClientType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return Responder::error(__('apis.unauthorize'), [], 401);
        }

        // Allow delivery users to access selected client routes (e.g., report-problem, invoice.download)
        if ($user->type !== 'client') {
            $currentRoute = optional($request->route())->getName();
            $deliveryAllowedRoutes = [
                'client.orders.report-problem',
                'client.orders.invoice.download',
            ];
            if ($user->type === 'delivery' && in_array($currentRoute, $deliveryAllowedRoutes, true)) {
                return $next($request);
            }

            return Responder::error(__('apis.unauthorize'), [], 403);
        }

        return $next($request);
    }
}
