<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

/**
 * Web Authentication Service
 * 
 * Handles web-based authentication logic for the website frontend
 */
class WebAuthService
{
    protected OTPService $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'country_code' => $data['country_code'],
            'email' => $data['email'],
            'password' => $data['password'], // Will be hashed by User model mutator
            'gender' => $data['gender'],
            'city_id' => $data['city_id'],
            'district_id' => $data['district_id'] ?? null,
            'type' => $data['type'] ?? 'client',
            'is_active' => false, // User needs to verify OTP first
        ]);

        // Generate and send OTP for verification
        $this->otpService->generateAndSendOTP($user);

        // Store user ID in session for OTP verification
        Session::put('pending_user_id', $user->id);
        Session::put('pending_user_phone', $user->phone);

        return $user;
    }

    /**
     * Verify registration OTP and activate user
     *
     * @param string $otp
     * @return array
     */
    public function verifyRegistrationOTP(string $otp): array
    {
        $userId = Session::get('pending_user_id');

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'جلسة التحقق منتهية. يرجى التسجيل مرة أخرى',
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ];
        }

        // Verify OTP
        if (!$this->otpService->verifyOTP($user, $otp)) {
            return [
                'success' => false,
                'message' => 'كود التفعيل غير صحيح أو منتهي الصلاحية',
            ];
        }

        // Activate user
        $this->otpService->activateUser($user);

        // Clear session data
        Session::forget(['pending_user_id', 'pending_user_phone']);

        return [
            'success' => true,
            'message' => __('site.account_activated_login'),
            'user' => $user,
        ];
    }

    /**
     * Authenticate user and create session
     *
     * @param string $phone
     * @param string $countryCode
     * @param string $password
     * @return array
     */
    public function login(string $phone, string $countryCode, string $password): array
    {
        // Find user by phone and country code
        $user = User::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->where('type', 'client')
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'رقم الجوال أو كلمة المرور غير صحيحة',
            ];
        }

        // Check if password is correct
        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'رقم الجوال أو كلمة المرور غير صحيحة',
            ];
        }

        // Check if user is blocked
        if ($user->is_blocked) {
            return [
                'success' => false,
                'message' => 'حسابك محظور. يرجى التواصل مع الإدارة',
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            // Resend OTP for activation
            $this->otpService->generateAndSendOTP($user);
            Session::put('pending_user_id', $user->id);

            return [
                'success' => false,
                'message' => 'حسابك غير مفعل. تم إرسال كود التفعيل إلى جوالك',
                'requires_activation' => true,
            ];
        }

        // Check if user account is soft deleted
        if ($user->trashed()) {
            return [
                'success' => false,
                'message' => 'حسابك محذوف. يرجى التواصل مع الإدارة',
            ];
        }

        // Log the user in with remember me
        Auth::guard('web')->login($user, true);

        return [
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
        ];
    }

    /**
     * Send password reset OTP
     *
     * @param string $phone
     * @param string $countryCode
     * @return array
     */
    public function sendPasswordResetOTP(string $phone, string $countryCode): array
    {
        // Find user by phone and country code
        $user = User::where('phone', $phone)
            ->where('country_code', $countryCode)
            ->where('type', 'client')
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'رقم الجوال غير مسجل',
            ];
        }

        // Generate and send OTP
        $this->otpService->generateAndSendOTP($user);

        // Store user ID in session for password reset
        Session::put('password_reset_user_id', $user->id);
        Session::put('password_reset_phone', $user->phone);

        return [
            'success' => true,
            'message' => 'تم إرسال كود التحقق إلى جوالك',
        ];
    }

    /**
     * Verify password reset OTP
     *
     * @param string $otp
     * @return array
     */
    public function verifyPasswordResetOTP(string $otp): array
    {
        $userId = Session::get('password_reset_user_id');

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'جلسة التحقق منتهية. يرجى المحاولة مرة أخرى',
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ];
        }

        // Verify OTP
        if (!$this->otpService->verifyOTP($user, $otp)) {
            return [
                'success' => false,
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية',
            ];
        }

        // Mark OTP as verified in session
        Session::put('password_reset_verified', true);

        return [
            'success' => true,
            'message' => 'تم التحقق من الكود بنجاح',
        ];
    }

    /**
     * Reset user password
     *
     * @param string $password
     * @return array
     */
    public function resetPassword(string $password): array
    {
        $userId = Session::get('password_reset_user_id');
        $verified = Session::get('password_reset_verified');

        if (!$userId || !$verified) {
            return [
                'success' => false,
                'message' => 'جلسة التحقق منتهية. يرجى المحاولة مرة أخرى',
            ];
        }

        $user = User::find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ];
        }

        // Update password
        $user->update(['password' => $password]); // Will be hashed by User model mutator

        // Clear OTP
        $this->otpService->clearOTP($user);

        // Clear session data
        Session::forget(['password_reset_user_id', 'password_reset_phone', 'password_reset_verified']);

        return [
            'success' => true,
            'message' => __('site.password_reset_success'),
        ];
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();
        Session::flush();
        Session::regenerate();
    }
}

