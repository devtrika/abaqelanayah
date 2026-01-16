<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderRating;
use App\Models\Admin;
use App\Notifications\NotifyAdmin;
use Illuminate\Support\Facades\Auth;

class RatingService
{
    /**
     * تقييم الطلب
     */
    public function rateOrder(array $data): void
    {
        $userId = Auth::id();
        $orderId = $data['order_id'];

        // حفظ التقييم
        OrderRating::create([
            'user_id'   => $userId,
            'order_id'  => $orderId,
            'rating'    => $data['rating'],
            'comment'   => $data['comment'] ?? null,
        ]);

        // جلب رقم الطلب
        $order = Order::findOrFail($orderId);
        $orderNum = $order->order_number ?? $order->id;

        // إرسال إشعار للإداريين
        $this->sendOrderRatedNotificationToAdmins($orderNum, $orderId, $userId);
    }

    /**
     * تحقق إذا كان الطلب تم تقييمه مسبقًا
     */
    public function isOrderAlreadyRated($orderId): bool
    {
        return OrderRating::where('user_id', Auth::id())->where('order_id', $orderId)->exists();
    }

    /**
     * إرسال إشعار للإداريين بأن الطلب تم تقييمه
     */
    protected function sendOrderRatedNotificationToAdmins($orderNum, $orderId, $userId): void
    {
        $messageAr = 'تم تقييم الطلب رقم #' . $orderNum . ' من المستخدم رقم #' . $userId;
        $messageEn = 'Order #' . $orderNum . ' has been rated by user #' . $userId;

        $admins = Admin::all();

        // Get the latest OrderRating for this order and user
        $orderRating = \App\Models\OrderRating::where('order_id', $orderId)
            ->where('user_id', $userId)
            ->latest('id')
            ->first();

        foreach ($admins as $admin) {
            $admin->notify(new NotifyAdmin([
                'title' => [
                    'ar' => 'تقييم طلب',
                    'en' => 'Order Rating',
                ],
                'body' => [
                    'ar' => $messageAr,
                    'en' => $messageEn,
                ],
                'type' => 'order_rate',
                'orderrate_id' => $orderRating ? $orderRating->id : null,
            ]));
        }
    }

    /**
     */
    public function getUserRatings()
    {
        return OrderRating::where('user_id', Auth::id())->latest()->get();
    }
}
