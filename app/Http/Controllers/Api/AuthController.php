<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Auth\Client\RegisterRequest;
use App\Models\User;
use App\Traits\SmsTrait;
use App\Models\Complaint;
use App\Facades\Responder;
use App\Models\UserUpdate;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\ClientResource;
use App\Http\Resources\Api\WalletResource;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ActivateRequest;
use App\Http\Requests\Api\Auth\ResendCodeRequest;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Http\Requests\Api\Auth\StoreComplaintRequest;
use App\Http\Requests\Api\Auth\UpdatePasswordRequest;
use App\Http\Requests\Api\Auth\VerifyResetCodeRequest;
use App\Http\Requests\Api\Auth\changePhoneSendCodeRequest;
use App\Http\Requests\Api\User\changeEmailSendCodeRequest;
use App\Http\Requests\Api\User\changeEmailCheckCodeRequest;
use App\Http\Requests\Api\User\changePhoneCheckCodeRequest;
use App\Http\Requests\Api\Auth\forgetPasswordSendCodeRequest;
use App\Http\Resources\Api\Notifications\NotificationsCollection;

class AuthController extends Controller {
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
    
    public function activate(ActivateRequest $request) {
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
            ->first()) {

            return Responder::error(__('auth.failed'));
        }

        if (!$this->isCodeCorrect($user, $request->code)) {
            return Responder::error(trans('auth.code_invalid'));
        }

        return Responder::success(['user' => $user->markAsActive()->login()], ['message' => __('auth.activated')]);
    }

public function login(LoginRequest $request)
{
    if (!$user = User::where('phone', $request['phone'])
        ->where('country_code', $request['country_code'])
        ->where('type', $request['type'])
        ->first()) {
        return Responder::error(__('auth.failed'), [], 404);
    }

    if (!Hash::check($request['password'], $user->password)) {
        return Responder::error(__('auth.failed'), [], 401);
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

    $user->updateUserDevice();

    return Responder::success(
        ['user' => $user->login()],
        ['message' => __('apis.signed')]
    );
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



    public function resendCode(ResendCodeRequest $request) {
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
            ->first()) {

            return Responder::error(__('auth.failed'));
        }
        $user->sendVerificationCode();

        return Responder::success([], ['message' => __('auth.code_re_send')]);
    }



    public function logout(Request $request) {
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                $user->logout();
            }
        }

        return Responder::success([], ['message' => __('apis.loggedOut')]);
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        $user = auth()->user();
        $user->update($request->validated());
        return Responder::success([], ['message' => __('apis.updated')]);
    }

    public function forgetPasswordSendCode(forgetPasswordSendCodeRequest $request) {
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
            ->first()) {

            return Responder::error(__('auth.failed'));
        }
        if (!$user) {
            return Responder::error(trans('site.user_wrong'));
        }
        UserUpdate::updateOrCreate(['user_id' => $user->id, 'type' => 'password'], ['code' => '']);
        return Responder::success([], ['message' => __('apis.success')]);
    }

    public function verifyPasswordResetCode(VerifyResetCodeRequest $request) {
        // Find the user
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
            ->first()) {
            return Responder::error(__('auth.failed'));
        }

        // Find the password reset record
        $passwordReset = UserUpdate::where([
            'user_id' => $user->id,
            'type' => 'password',
            'code' => $request->code
        ])->first();

        // Check if code exists
        if (!$passwordReset) {
            return Responder::error(trans('auth.code_invalid'));
        }

        // Check if code is expired (assuming there's a created_at timestamp)
        $codeCreatedAt = $passwordReset->created_at;
        $codeExpiresAt = $codeCreatedAt->addMinutes(60); // Code expires after 60 minutes

        // if (now()->gt($codeExpiresAt)) {
        //     return Responder::error(trans('auth.code_expired'));
        // }

        // Code is valid and not expired
        return Responder::success([], ['message' => trans('auth.code_valid')]);
    }

    public function resetPassword(ForgetPasswordRequest $request) {
        // Find the user
        if (!$user = User::where('phone', $request['phone'])
            ->where('country_code', $request['country_code'])
            ->first()) {
            return Responder::error(__('auth.failed'));
        }

        // Find the password reset record
        $passwordReset = UserUpdate::where([
            'user_id' => $user->id,
            'type' => 'password',
            'code' => $request->code
        ])->first();

        if (!$passwordReset) {
            return Responder::error(trans('auth.code_invalid'));
        }

        // Update the user's password
        $user->update(['password' => $request->password]);

        // Delete the password reset record
        $passwordReset->delete();

        return Responder::success([], ['message' => trans('auth.password_reset_success')]);
    }

    public function StoreComplaint(StoreComplaintRequest $request) {
        Complaint::create($request->validated() + (['user_id' => auth()->id()]));
        return Responder::success([], ['message' => __('apis.complaint_send')]);
    }
        public function getProfile(Request $request)
    {
        $user = auth()->user()->load('branches.managers');
            $userData = ClientResource::make($user);
        
        return Responder::success($userData);
    }


    public function sendPhoneUpdateCode(\App\Http\Requests\Api\Auth\Provider\SendPhoneUpdateCodeRequest $request)
    {
        $user = auth()->user();

        // Check if old phone matches current user phone
        if ($user->phone !== $request->old_phone || $user->country_code !== $request->country_code) {
            return Responder::error(__('auth.old_phone_mismatch'));
        }

        // Check if new phone is already taken by another user
        $existingUser = User::where('phone', $request->new_phone)
            ->where('country_code', $request->country_code)
            ->where('id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingUser) {
            return Responder::error(__('auth.phone_already_exists'));
        }

        // Create phone update record with verification code
        $phoneUpdate = $user->userUpdates()->create([
            'type' => 'phone',
            'phone' => $request->new_phone,
            'country_code' => $request->country_code,
            'code' => null, // This will trigger the setCodeAttribute to generate a code
        ]);

        // Send SMS with verification code
        $fullPhone = $request->country_code . $request->new_phone;
        $message = __('apis.phone_update_code') . ': ' . $phoneUpdate->code;
        $this->sendSms($fullPhone, $message);

        return Responder::success([], ['message' => __('apis.verification_code_sent')]);
    }

    /**
     * Verify code and update phone number
     */
    public function verifyPhoneUpdateCode(\App\Http\Requests\Api\Auth\Provider\VerifyPhoneUpdateCodeRequest $request)
    {
        $user = auth()->user();

        // Get the latest phone update request
        $phoneUpdate = UserUpdate::getLatestPhoneUpdate($user->id);

        if (!$phoneUpdate) {
            return Responder::error(__('auth.no_pending_phone_update'));
        }

        // Check if code is expired
        if ($phoneUpdate->isExpired()) {
            return Responder::error(__('auth.code_expired'));
        }

        // Check if code matches
        if ($phoneUpdate->code !== $request->code) {
            return Responder::error(__('auth.invalid_code'));
        }

        // Check if new phone is still available (double check)
        $existingUser = User::where('phone', $phoneUpdate->phone)
            ->where('country_code', $phoneUpdate->country_code)
            ->where('id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingUser) {
            return Responder::error(__('auth.phone_already_exists'));
        }

        // Update user's phone number
        $user->update([
            'phone' => $phoneUpdate->phone,
            'country_code' => $phoneUpdate->country_code,
        ]);

        // Delete the phone update record as it's no longer needed
        $phoneUpdate->delete();

        // Load relationships for the resource
        $user->load(['provider.bankAccount', 'city']);

        $requestToken = ltrim($request->header('authorization'), 'Bearer ');

        return Responder::success([], ['message' => __('apis.phone_updated_successfully')]);
    }




    /**
     * Verify the password reset code
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function changeLang(Request $request) {
        $user = auth()->user();
        $lang = in_array($request->lang, languages()) ? $request->lang : 'ar';
        $user->update(['lang' => $lang]);
        App::setLocale($lang);
        return Responder::success([], ['message' => __('apis.updated')]);
    }

    public function switchNotificationStatus() {
        $user = auth()->user();
        $user->update(['is_notify' => !$user->is_notify]);
        return Responder::success(['notify' => (bool) $user->refresh()->is_notify], ['message' => __('apis.updated')]);
    }

    public function getNotifications() {
        // auth()->user()->unreadNotifications->markAsRead();
        $notifications = new NotificationsCollection(auth()->user()->notifications()->get());
        return Responder::paginated( $notifications);
    }

    public function countUnreadNotifications() {
        return Responder::success(['count' => auth()->user()->unreadNotifications->count()]);
    }

    /**
     * Request account deletion
     */
    public function requestAccountDeletion(\App\Http\Requests\Api\AccountDeletionRequest $request)
    {
        $user = auth()->user();

        // Check if user already has a pending deletion request
        $existingRequest = \App\Models\AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending','approved'])
            ->first();

        if ($existingRequest) {
            return Responder::error(__('apis.deletion_request_already_exists'));
        }

        // Create new deletion request
        $deletionRequest = \App\Models\AccountDeletionRequest::create([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        // Send notification to admins (optional)
        $admins = \App\Models\Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'طلب حذف حساب جديد',
                    'en' => 'New Account Deletion Request'
                ],
                'body' => [
                    'ar' => 'تم تقديم طلب حذف حساب من المستخدم: ' . $user->name,
                    'en' => 'Account deletion request submitted by user: ' . $user->name
                ],
                'type' => 'account_deletion_request',
                'user_id' => $user->id,
                'request_id' => $deletionRequest->id
            ]));
        }

        return Responder::success(null, ['message' => __('apis.deletion_request_submitted')]);
    }

    public function markNotificationAsRead($notification_id)
    {
        $notification = auth()->user()->notifications()->find($notification_id);
    
        if (!$notification) {
            return Responder::error( __('apis.not_found') );
        }
    
        $notification->markAsRead();
    
        return Responder::success([],  __('apis.success'));
    }
    
    public function deleteNotifications() {
        auth()->user()->notifications()->delete();
        return Responder::success([], ['message' => __('apis.deleted')]);
    }


        public function MarkAsReadNotifications() {
        auth()->user()->unreadNotifications->markAsRead();
        return Responder::success([], ['message' => __('apis.success')]);
    }


    public function changePhoneSendCode(changePhoneSendCodeRequest $request) {
        $update = UserUpdate::updateOrCreate([
            'user_id'      => auth()->id(),
            'type'         => 'phone',
            'country_code' => $request->country_code,
            'phone'        => $request->phone,
        ], [
            'code' => '',
        ])->refresh();
        auth()->user()->sendCodeAtSms($update->code, $update->full_phone);
        return Responder::success([], ['message' => __('apis.success')]);
    }

    public function changePhoneCheckCode(changePhoneCheckCodeRequest $request) {
        $update = UserUpdate::where(['user_id' => auth()->id(), 'type' => 'phone', 'code' => $request->code])->first();
        if (!$update) {
            return Responder::error(trans('auth.code_invalid'));
        }
        auth()->user()->update(['phone' => $update->phone, 'country_code' => $update->country_code]);
        $update->delete();
        return Responder::success([], ['message' => __('apis.success')]);
    }

    public function changeEmailSendCode(changeEmailSendCodeRequest $request) {
        UserUpdate::updateOrCreate([
            'user_id' => auth()->id(),
            'type'    => 'email',
            'email'   => $request->email,
        ], [
            'code' => '',
        ]);
        return Responder::success([], ['message' => __('apis.success')]);
    }

    public function changeEmailCheckCode(changeEmailCheckCodeRequest $request) {
        $update = UserUpdate::where(['user_id' => auth()->id(), 'type' => 'email', 'code' => $request->code])->first();
        if (!$update) {
            return Responder::error(trans('auth.code_invalid'));
        }
        auth()->user()->update(['email' => $update->email]);
        $update->delete();
        return Responder::success([], ['message' => __('apis.success')]);
    }

    public function deleteAccount() {
        // if there any delete conditions write it here
        auth()->user()->delete();
        return Responder::success([], ['message' => __('auth.account_deleted')]);
    }

    public function getWalletTransactions()
    {
        $transactions = WalletTransaction::where('user_id', auth()->id())->get();
        return Responder::success([
            'transactions' => WalletResource::collection($transactions),
            'balance' => auth()->user()->wallet_balance,
        ]);
    }


    public function switchAcceptOrders() {
        $user = auth()->user();
        if (!$user || $user->type !== 'delivery') {
            return Responder::error(__('apis.must_be_delivery'), 403);
        }

        $user->update(['accept_orders' => !$user->accept_orders]);
        return Responder::success(['accept_orders' => (bool) $user->refresh()->accept_orders], ['message' => __('apis.updated')]);
    }

    


}

