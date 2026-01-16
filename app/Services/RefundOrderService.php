<?php

namespace App\Services;

use App\Models\RefundOrder;

class RefundOrderService
{
    /**
     * Get refund orders with optional filters
     */
    public function getRefundOrders(array $filters = [])
    {
        $user = auth()->user();
        $query = RefundOrder::with(['order', 'delivery', 'user']);
        if ($user && $user->type === 'delivery') {
            // Show refunds either assigned directly to this delivery user
            // or belonging to orders that are assigned to this delivery user
            $query->where(function($q) use ($user) {
                $q->where('delivery_id', $user->id)
                  ->orWhereHas('order', function($oq) use ($user) {
                      $oq->where('delivery_id', $user->id);
                  });
            });
        }
    return $query->search($filters)->get();
    }

    /**
     * Get single refund order
     */
    public function getRefundOrderById($id)
    {
        $user = auth()->user();
        $query = RefundOrder::with(['order', 'delivery', 'user']);
        if ($user && $user->type === 'delivery') {
            $query->where(function($q) use ($user) {
                $q->where('delivery_id', $user->id)
                  ->orWhereHas('order', function($oq) use ($user) {
                      $oq->where('delivery_id', $user->id);
                  });
            });
        }
        return $query->find($id);
    }

    /**
     * Update refund order status and the original order status
     */
    public function updateRefundStatus(RefundOrder $refundOrder, string $action)
    {
        // Prevent updating to the same status
        if ($refundOrder->status === $action) {
            throw new \Exception('لا يمكن تحديث الحالة لنفس الحالة الحالية');
        }

        // Prevent delivered before out-for-delivery
        if ($action === 'delivered' && $refundOrder->status !== 'out-for-delivery') {
            throw new \Exception(__('apis.refund_must_be_out_for_delivery_before_delivered'));
        }

        if ($action === 'confirm') {
            if ($refundOrder->order) {
                $refundOrder->order->refund_status = 'request_refund';
                $refundOrder->order->save();
            }
        }

        $refundOrder->status = $action;
        $refundOrder->save();
        return $refundOrder;
    }
}
