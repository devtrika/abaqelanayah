<?php

namespace App\Traits;

use App\Models\User;
use App\Services\Responder;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\UserResource;

trait ResponseTrait {

  /**
   * keys : success, fail, needActive, waitingApprove, unauthenticated, blocked, exception
   */
  //todo: user builder design pattern
  public function response($key, $msg, $data = [], $anotherKey = [], $page = false) {

    $allResponse['key'] = (string) $key;
    $allResponse['msg'] = (string) $msg;

    # unread notifications count if request ask
    if ('success' == $key && request()->has('count_notifications')) {
      $count = 0;
      if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();
        $count = $user->notifications()->unread()->count();
      }

      $allResponse['count_notifications'] = $count;
    }

    # additional data
    if (!empty($anotherKey)) {
      foreach ($anotherKey as $otherkey => $value) {
        $allResponse[$otherkey] = $value;
      }
    }

    # res data
    if ([] != $data && (in_array($key, ['success', 'needActive', 'exception', 'validation']))) {
      $allResponse['data'] = $data;
    }

    return response()->json($allResponse , $this->getCode($key));
  }

  public function unauthenticatedReturn() {
    return Responder::error(trans('auth.unauthenticated'), [], 401);
  }

  public function unauthorizedReturn($otherData) {
    return Responder::error(trans('auth.not_authorized'), [], 403);
  }

  public function blockedReturn($user) {
    $user->logout();
    return Responder::error(__('auth.blocked'), [], 423);
  }

  public function phoneActivationReturn($user) {
    $user->sendVerificationCode();
    return Responder::error(__('auth.not_active'), [], 203);
  }

  public function failMsg($msg) {
    return Responder::error($msg, [], 400);
  }

  public function successMsg($msg = 'done') {
    return Responder::success($msg);
  }

  public function successData($data) {
    return Responder::success($data);
  }

  public function successOtherData(array $dataArr) {
    return Responder::success($dataArr);
  }

  public function getCodeMatch($key) {

    // $code = match($key) {
    //   'success' => 200,
    //   'fail' => 400,
    //   'unauthorized' => 400,
    //   'needActive' => 203,
    //   'unauthenticated' => 401,
    //   'blocked' => 423,
    //   'exception' => 500,
    //   default => '200',
    // };

    // return $code;
  }

  public function getCode($key) {
    switch ($key) {
    case 'success':
      $code = 200;
      break;
    case 'fail':
      $code = 400;
      break;
    case 'needActive':
      $code = 203;
      break;
    case 'unauthorized':
      $code = 400;
      break;
    case 'unauthenticated':
      $code = 401;
      break;
    case 'blocked':
      $code = 423;
      break;
    case 'exception':
      $code = 500;
      break;
    case 'validation':
      $code = 422;
      break;

    default:
      $code = 200;
      break;

    }

    return $code;
  }

}