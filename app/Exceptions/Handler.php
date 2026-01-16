<?php

namespace App\Exceptions;

use App\Facades\Responder;
use App\Traits\ResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler {
  use ResponseTrait;

  protected $dontReport = [
    //
  ];

  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  public function report(Throwable $exception) {
    parent::report($exception);
  }

  public function render($request, Throwable $exception) {
    if ($request->is('api/*') || $request->expectsJson()) {
      // Handle validation exceptions
      if ($exception instanceof ValidationException) {
        return Responder::error(
          Arr::first(Arr::first($exception->errors())),
          $exception->errors(),
          422
        );
      }

      if ($exception instanceof ModelNotFoundException) {
        return Responder::error(__('apis.model_not_found'), [], 404);
      }

      if ($exception instanceof NotFoundHttpException) {
        return Responder::error(__('apis.route_not_found'), [], 404);
      }

      if ($exception instanceof AuthenticationException) {
        return $this->unauthenticatedReturn();
      }

      // Handle general server errors
      $message = $exception->getMessage();
      $debugData = [];

      // Add debug info in non-production environments
      if (config('app.debug')) {
        $debugData = [
          'line' => $exception->getLine(),
          'file' => $exception->getFile(),
          'trace' => $exception->getTraceAsString()
        ];
      }

      return Responder::error(
        $message ?: 'Internal Server Error',
        $debugData,
        500
      );
    }

    return parent::render($request, $exception);
  }

  public function unauthenticated($request, AuthenticationException $exception) {
    if ($request->expectsJson() || $request->is('api/*')) {
        return $this->unauthenticatedReturn();
    }

    // Check if the request is for the admin dashboard
    if ($request->is('admin/*')) {
        return redirect()->guest(route('admin.show.login'));
    }

    // Redirect to website login page for other web requests
    return redirect()->guest(route('website.login'));
  }
}
