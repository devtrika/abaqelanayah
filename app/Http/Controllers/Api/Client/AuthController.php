<?php

namespace App\Http\Controllers\Api\Client;

use App\Models\User;
use App\Traits\SmsTrait;
use App\Facades\Responder;
use Illuminate\Support\Str;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\UserLoginVerificationCode;
use App\Http\Resources\Api\ClientResource;
use App\Http\Requests\Api\Auth\Client\LoginRequest;
use App\Http\Requests\Api\Auth\Client\RegisterRequest;

class AuthController extends Controller
{
    use ResponseTrait, SmsTrait, GeneralTrait;

    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();
        
        // Create the user first
        $user = User::create($userData);
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $user->addMedia($request->file('image'))
                ->toMediaCollection('profile');
        }
        
        $user->sendVerificationCode();

        $userData = new ClientResource($user->refresh());
        return Responder::success(['user' => $userData], ['message' => __('auth.registered')]);
    }

    public function login(LoginRequest $request)
    {
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
              ->where('type' , $request['type'])
            ->first()) {
            return Responder::error(__('auth.failed') , [] , 404);
        }

     
        
        if ($user->is_blocked) {
            $user->logout();
            return Responder::error(__('auth.blocked'), [], 423);
        }

        if (!$user->is_active) {
            $data = $user->sendVerificationCode();
            return Responder::error(__('auth.not_active'), [], 412);
        }

        // Check if user account is deleted (soft deleted)
        if ($user->trashed()) {
            return Responder::error(__('auth.account_deleted'), [], 410);
        }

            $code = generateRandomCode();
            $expiresAt = now()->addMinutes(5);
        UserLoginVerificationCode::storeCode($user->id, $code, $expiresAt);

        
        return Responder::success([], ['message' => __('apis.verification_code_sent')]);
    }


    public function verifyLoginCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'country_code' => 'required|string',
            'code' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $user = User::where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->where('type', 'client')
            ->first();

        if (!$user) {
            return Responder::error(__('auth.failed'), [], 404);
        }

        $verificationCode = UserLoginVerificationCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('is_blocked', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$verificationCode) {
            // Find the latest unblocked, unexpired code for this user
            $latestCode = UserLoginVerificationCode::where('user_id', $user->id)
                ->where('is_blocked', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();
            if ($latestCode) {
                $latestCode->attempts = $latestCode->attempts + 1;
                if ($latestCode->attempts >= 3) {
                    $latestCode->is_blocked = true;
                    $latestCode->save();
                    return Responder::error(__('auth.code_expired'), [], 422);
                }
                $latestCode->save();
            }
            return Responder::error(__('auth.invalid_code'), [], 422);
        }

        // Mark code as used
        $verificationCode->is_blocked = true;
        $verificationCode->save();

        // Log the user in
        $token = $user->login();

       
        $user->updateUserDevice();

        return Responder::success(['user' => $user->login()], ['message' => __('apis.signed')]);
    }

       public function updatePassword(UpdatePasswordRequest $request) {
        $user = auth()->user();
        $user->update($request->validated());
        return Responder::success([], ['message' => __('apis.updated')]);
    }

    public function updateProfile(\App\Http\Requests\Api\Auth\Client\UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        
        // Handle image separately
        if ($request->hasFile('image')) {
            $user->clearMediaCollection('profile');
            $user->addMedia($request->file('image'))
                ->toMediaCollection('profile');
        }
        
        $user->update($data);
        
        $requestToken = ltrim($request->header('authorization'), 'Bearer ');
        $userData = ClientResource::make($user->refresh())->setToken($requestToken);
        
        if (!$user->is_active) {
            $data = $user->sendVerificationCode();
            return Responder::error(__('auth.not_active'), $data, 203);
        }
        
        return Responder::success(['user' => $userData], ['message' => __('apis.updated')]);
    }

    public function setLocation(Request $request){
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = auth()->user();
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        $user->save();

        return Responder::success([], ['message' => __('apis.updated')]);
    }
  

    
}


