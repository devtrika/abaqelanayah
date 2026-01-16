<?php

namespace App\Traits;

use App\Models\Permission;
use Illuminate\Support\Facades\Route;

trait AdminFirstRouteTrait {
  public function getAdminFirstRouteName($authRoutes = null) {
    $routeName = null;

    if (!$authRoutes) {
      $authRoutes = Permission::where('role_id', auth()->guard('admin')->user()->role_id)
        ->pluck('permission')
        ->toArray();
    }

    $routes = Route::getRoutes();

    foreach ($routes as $route) {
      if (isset($route->getAction()['icon'])
        && in_array($route->getName(), $authRoutes)) {

        if (!isset($route->getAction()['sub_route'])
          || false == $route->getAction()['sub_route']) {

          $routeName = $route->getName();
          break;
        }

      }
    }

    // If no route with icon found, use the first available permission
    if (!$routeName && count($authRoutes) > 0) {
      $routeName = $authRoutes[0];
    }

    // Final fallback to dashboard
    if (!$routeName) {
      $routeName = 'dashboard';
    }

    return $routeName;
  }
}