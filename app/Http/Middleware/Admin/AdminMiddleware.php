<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware {
  public function handle($request, Closure $next) {
    // Check if admin is not authenticated or has invalid role or is blocked
    if (!Auth::guard('admin')->check()
      || Auth::guard('admin')->user()->role_id <= 0
      || Auth::guard('admin')->user()->is_blocked == 1) {

        auth('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();

        // For AJAX requests, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => __('auth.unauthenticated'),
                'redirect' => route('admin.show.login')
            ], 401);
        }

        // Store the intended URL for redirect after login
        if (!$request->is('admin/login')) {
            session()->put('beforeLoginUrl', url()->current());
        }

        return redirect()->route('admin.show.login');
    }

    // Redirect to intended URL after successful login
    if (session()->has('beforeLoginUrl')) {
      $currentUrl = session()->get('beforeLoginUrl');
      session()->remove('beforeLoginUrl');
      return redirect()->to($currentUrl);
    }

    return $next($request);
  }
}
