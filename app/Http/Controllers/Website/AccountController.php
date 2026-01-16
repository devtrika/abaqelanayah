<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\City;
use App\Models\District;
use App\Services\NotificationService;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cities = City::orderBy('name')->get();
        $districts = ($user && $user->city_id)
            ? District::where('city_id', $user->city_id)->where('status', 1)->orderBy('name')->get()
            : collect();

        return view('website.pages.account.account', compact('cities', 'districts'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['nullable', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->id)],
            'country_code' => ['nullable', 'string', 'max:5'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'city_id' => ['nullable', 'integer'],
            'district_id' => ['nullable', 'integer'],
        ]);

        // Normalize phone: rely on User model mutators (setPhoneAttribute, setCountryCodeAttribute)
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'] ?? $user->country_code,
            'gender' => $validated['gender'] ?? $user->gender,
            'city_id' => $validated['city_id'] ?? $user->city_id,
            'district_id' => $validated['district_id'] ?? $user->district_id,
        ]);

        $user->save();

        return redirect()->route('website.account')->with('success', 'تم تحديث بيانات الحساب بنجاح');
    }

    public function password()
    {
        return view('website.pages.account.password');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'min:6', 'max:191'],
            'password' => ['required', 'string', 'min:6', 'max:191', 'different:current_password', 'confirmed'],
        ], [
            'current_password.required' => 'الرجاء إدخال كلمة المرور الحالية',
            'current_password.min' => 'كلمة المرور الحالية يجب ألا تقل عن 6 أحرف',
            'password.required' => 'الرجاء إدخال كلمة المرور الجديدة',
            'password.min' => 'كلمة المرور الجديدة يجب ألا تقل عن 6 أحرف',
            'password.different' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة'])->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('website.password')->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    public function notifications(Request $request)
    {
        $user = $request->user();

        $notifications = $user
            ? $user->notifications()->latest()->get()
            : collect();

        return view('website.pages.account.notifications', compact('notifications'));
    }

    /**
     * Store Firebase device token for the authenticated user (web devices).
     */
    public function storeDeviceToken(Request $request, NotificationService $notificationService)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'device_token' => ['required', 'string'],
            'device_id' => ['nullable', 'string'],
            'device_type' => ['nullable', 'string'],
        ]);

        $attributes = [
            'device_type' => $validated['device_type'] ?? 'web',
            'device_id' => $validated['device_id'] ?? $validated['device_token'],
            'is_active' => true,
        ];

        $notificationService->registerDeviceToken($user->id, $validated['device_token'], $attributes);

        return response()->json(['success' => true]);
    }

    /**
     * Redirect to the appropriate page for a notification and mark it as read.
     */
    public function notificationGo(Request $request, string $id)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('website.login');
        }

        $notification = $user->notifications()->where('id', $id)->first();
        if (!$notification) {
            return redirect()->route('website.notifications');
        }

        // Mark as read
        if (method_exists($notification, 'markAsRead')) {
            $notification->markAsRead();
        } else {
            $notification->read_at = now();
            $notification->save();
        }

        $data = is_array($notification->data) ? $notification->data : [];
        $orderId = $data['order_id'] ?? null;
        $orderType = $data['order_type'] ?? 'normal';

        // If this notification relates to an order/refund, redirect accordingly
        if ($orderId) {
            if ($orderType === 'refund') {
                return redirect()->route('website.refunds.show', $orderId);
            }
            return redirect()->route('website.orders.show', $orderId);
        }

        // Fallback: back to notifications page
        return redirect()->route('website.notifications');
    }

    public function deleteNotifications(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->notifications()->delete();
        }

        return redirect()->route('website.notifications')
            ->with('success', __('site.notifys_deleted'));
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('website.login')
                ->with('error', __('site.account_delete_error'));
        }

        try {
            // Log the user out first
            auth()->guard('web')->logout();

            // Delete the user account (soft delete)
            $user->delete();

            // Clear session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('website.login')
                ->with('success', __('site.account_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->route('website.account')
                ->with('error', __('site.account_delete_error'));
        }
    }
}

