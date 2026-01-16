<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\NotificationService;

class CouponCreatedNotification extends Notification
{
    use Queueable;

    protected array $data;
    protected array $channels;
    protected array $firebaseResponse = [];

    public function __construct(array $data = [], array $channels = null)
    {
        $this->data = $data;
        $this->channels = $channels ?? [
            NotificationService::CHANNEL_DATABASE,
            NotificationService::CHANNEL_FIREBASE,
        ];
    }

    public function via($notifiable): array
    {
        return $this->channels;
    }

    public function toArray($notifiable): array
    {
        $discountText = $this->formatDiscount($this->data['discount'] ?? null, $this->data['discount_type'] ?? null);

        return [
            'type' => 'coupon_created',
            'title' => [
                'ar' => __("notification.title_coupon_created", [], 'ar'),
                'en' => __("notification.title_coupon_created", [], 'en'),
            ],
            'body' => [
                'ar' => __(
                    'notification.body_coupon_created',
                    [
                        'name' => $this->data['coupon_name'] ?? '',
                        'code' => $this->data['coupon_code'] ?? '',
                        'start_date' => $this->data['start_date'] ?? '',
                        'end_date' => $this->data['end_date'] ?? '',
                        'discount' => $discountText,
                    ],
                    'ar'
                ),
                'en' => __(
                    'notification.body_coupon_created',
                    [
                        'name' => $this->data['coupon_name'] ?? '',
                        'code' => $this->data['coupon_code'] ?? '',
                        'start_date' => $this->data['start_date'] ?? '',
                        'end_date' => $this->data['end_date'] ?? '',
                        'discount' => $discountText,
                    ],
                    'en'
                ),
            ],
            'data' => [
                'coupon_name' => $this->data['coupon_name'] ?? null,
                'coupon_code' => $this->data['coupon_code'] ?? null,
                'start_date'  => $this->data['start_date'] ?? null,
                'end_date'    => $this->data['end_date'] ?? null,
                'discount'    => $this->data['discount'] ?? null,
                'discount_type' => $this->data['discount_type'] ?? null,
            ],
        ];
    }

    public function toFirebase($notifiable): array
    {
        $payload = $this->toArray($notifiable);
        // Choose title/body depending on app locale; fall back to English
        $locale = app()->getLocale();
        $title = is_array($payload['title']) ? ($payload['title'][$locale] ?? $payload['title']['en'] ?? '') : ($payload['title'] ?? '');
        $body  = is_array($payload['body']) ? ($payload['body'][$locale] ?? $payload['body']['en'] ?? '') : ($payload['body'] ?? '');

        return [
            'title' => $title,
            'body' => $body,
            'data' => array_merge($payload['data'] ?? [], [
                'type' => 'coupon_created',
            ]),
            'priority' => 'high',
        ];
    }

    public function setFirebaseResponse(array $response): void
    {
        $this->firebaseResponse = $response;
    }

    protected function formatDiscount($discount, $type): string
    {
        if ($discount === null || $discount === '') {
            return '';
        }

        // Common types: 'ratio' => percentage, 'number' => fixed amount
        if ($type === 'ratio') {
            // Ensure integer percentage if decimal passed
            $percentage = is_numeric($discount) ? (float)$discount : 0;
            // Strip trailing .0
            $percentageStr = rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.');
            return $percentageStr . '%';
        }

        // Default: fixed amount
        // You can append currency if needed, but keep it raw as the app might add currency symbol separately
        if (is_numeric($discount)) {
            // Remove trailing .00
            $amountStr = rtrim(rtrim(number_format((float)$discount, 2, '.', ''), '0'), '.');
            return $amountStr;
        }

        return (string) $discount;
    }
}

