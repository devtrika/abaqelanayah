<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware {
  use ResponseTrait;

  protected function redirectTo($request) {
    if (!$request->is('api/*')) {
      // Check if it's an admin request
      if ($request->is('admin/*')) {
        return route('admin.show.login');
      }

      // Otherwise redirect to website login
      return route('website.login');
    }
  }
}
