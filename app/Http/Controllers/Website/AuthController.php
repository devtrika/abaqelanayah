<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Auth\LoginRequest;
use App\Http\Requests\Website\Auth\RegisterRequest;
use App\Http\Requests\Website\Auth\VerifyRegisterOtpRequest;
use App\Http\Requests\Website\Auth\ForgotPasswordRequest;
use App\Http\Requests\Website\Auth\VerifyPasswordOtpRequest;
use App\Http\Requests\Website\Auth\ResetPasswordRequest;
use App\Services\Auth\WebAuthService;
use App\Services\Auth\OTPService;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

/**
 * Website Authentication Controller
 * 
 * Handles all authentication-related requests from the website frontend
 */
class AuthController extends Controller
{
    protected WebAuthService $authService;
    protected OTPService $otpService;

    public function __construct(WebAuthService $authService, OTPService $otpService)
    {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }

    /**
     * Show registration form
     *
     * @return View
     */
    public function showRegisterForm(): View
    {
        $cities = City::orderBy('name')->get();
        return view('website.auth.register', compact('cities'));
    }

    /**
     * Handle user registration
     *
     * @param RegisterRequest $request
     * @return RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            // Log the validated data for debugging
            \Log::info('Registration attempt', $request->validated());

            $user = $this->authService->register($request->validated());

            return redirect()
                ->route('website.register_otp')
                ->with('success', 'تم إرسال كود التفعيل إلى جوالك');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Registration error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء التسجيل: ' . $e->getMessage());
        }
    }

    /**
     * Handle registration OTP verification
     *
     * @param VerifyRegisterOtpRequest $request
     * @return RedirectResponse
     */
    public function verifyRegistrationOTP(VerifyRegisterOtpRequest $request): RedirectResponse
    {
        $result = $this->authService->verifyRegistrationOTP($request->input('otp'));

        if (!$result['success']) {
            return redirect()
                ->back()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('website.register_sucess')
            ->with('success', $result['message']);
    }

    /**
     * Resend registration OTP
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function resendRegistrationOTP(Request $request): JsonResponse|RedirectResponse
    {
        $userId = Session::get('pending_user_id');

        if (!$userId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('site.session_expired_try_again')
                ], 400);
            }
            return redirect()
                ->route('website.register')
                ->with('error', __('site.session_expired_try_again'));
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('site.user_not_found')
                ], 404);
            }
            return redirect()
                ->route('website.register')
                ->with('error', __('site.user_not_found'));
        }

        $this->otpService->resendOTP($user);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('site.code_resent_successfully')
            ]);
        }

        return redirect()
            ->back()
            ->with('success', __('site.code_resent_successfully'));
    }

    /**
     * Handle user login
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $result = $this->authService->login(
            $request->input('phone'),
            $request->input('country_code'),
            $request->input('password')
        );

        if (!$result['success']) {
            // Check if user needs activation
            if (isset($result['requires_activation']) && $result['requires_activation']) {
                return redirect()
                    ->route('website.register_otp')
                    ->with('info', $result['message']);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        // Redirect to home or dashboard after successful login
        return redirect()
            ->intended(route('website.home', [], false) ?? '/')
            ->with('success', $result['message']);
    }

    /**
     * Handle forgot password request
     *
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        $result = $this->authService->sendPasswordResetOTP(
            $request->input('phone'),
            $request->input('country_code')
        );

        if (!$result['success']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('website.password_otp')
            ->with('success', $result['message']);
    }

    /**
     * Handle password reset OTP verification
     *
     * @param VerifyPasswordOtpRequest $request
     * @return RedirectResponse
     */
    public function verifyPasswordOTP(VerifyPasswordOtpRequest $request): RedirectResponse
    {
        $result = $this->authService->verifyPasswordResetOTP($request->input('otp'));

        if (!$result['success']) {
            return redirect()
                ->back()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('website.password_reset')
            ->with('success', $result['message']);
    }

    /**
     * Resend password reset OTP
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendPasswordOTP(Request $request)
    {
        $userId = Session::get('password_reset_user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => __('site.session_expired_try_again'),
            ], 400);
        }

        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('site.user_not_found'),
            ], 404);
        }

        // Resend OTP
        $this->otpService->resendOTP($user);

        return response()->json([
            'success' => true,
            'message' => __('site.code_resent_successfully'),
        ]);
    }

    /**
     * Handle password reset
     *
     * @param ResetPasswordRequest $request
     * @return RedirectResponse
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $result = $this->authService->resetPassword($request->input('password'));

        if (!$result['success']) {
            return redirect()
                ->back()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('website.login')
            ->with('success', $result['message']);
    }

    /**
     * Handle user logout
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()
            ->route('website.login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }
}

