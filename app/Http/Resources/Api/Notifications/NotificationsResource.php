<?php
namespace App\Http\Resources\Api\Notifications;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResource extends JsonResource
{

    public function toArray($request)
    {
        $locale = app()->getLocale();
        $title  = $this->title;
        $body   = $this->body;
        // If title/body are arrays, get the value for the current locale
        if (is_array($title)) {
            $title = $title[$locale] ?? ($title['ar'] ?? reset($title));
        }
        if (is_array($body)) {
            $body = $body[$locale] ?? ($body['ar'] ?? reset($body));
        }

        // Determine order_type: prefer stored value, then infer from type key, then fallback by loading order if needed
        $orderType = $this->data['order_type'] ?? null;
        if ($orderType === null) {
            $typeKey = (string)($this->data['type'] ?? '');
            $lower   = function_exists('mb_strtolower') ? mb_strtolower($typeKey) : strtolower($typeKey);
            if (str_contains($lower, 'refund') || str_contains($lower, 'cancel')) {
                $orderType = 'refund';
            }
        }
        if ($orderType === null && !empty($this->data['order_id'])) {
            try {
                $order = \App\Models\Order::find($this->data['order_id']);
                if ($order) {
                    if (method_exists($order, 'isRefund') && $order->isRefund()) {
                        $orderType = 'refund';
                    } elseif (!empty($order->refund_number) || (bool)($order->refundable ?? false) || (bool)($order->is_refund ?? false)) {
                        $orderType = 'refund';
                    } else {
                        $orderType = 'normal';
                    }
                }
            } catch (\Throwable $e) {
                // ignore and leave as null
            }
        }

        return [
            'id'         => $this->id,
            // This 'type' is the notification type label (translated), not the order type
            'type'       => __('apis.' . ($this->data['type'] ?? '')),
            'title'      => $title,
            'body'       => $body,
            'is_read'    => $this->read_at !== null,
            'created_at' => $this->created_at->diffForHumans(),
            'order_id'   => $this->data['order_id'] ?? null,
            // Expose order_type in API so apps can route to the correct screen
            'order_type' => $orderType,

            // 'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
