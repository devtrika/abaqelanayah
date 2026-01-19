<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * OrderRepository
 * 
 * Handles all database operations for orders
 * Following the Repository pattern for data access layer
 */
class OrderRepository
{
    /**
     * Create a new order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Find order by ID
     *
     * @param int $id
     * @return Order|null
     */
    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * Find order by ID or fail
     *
     * @param int $id
     * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Order
    {
        return Order::findOrFail($id);
    }

    /**
     * Find order by order number
     *
     * @param string $orderNumber
     * @return Order|null
     */
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::where('order_number', $orderNumber)->first();
    }

    /**
     * Update order
     *
     * @param Order $order
     * @param array $data
     * @return bool
     */
    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    /**
     * Delete order (soft delete)
     *
     * @param Order $order
     * @return bool|null
     */
    public function delete(Order $order): ?bool
    {
        return $order->delete();
    }

    /**
     * Restore soft deleted order
     *
     * @param Order $order
     * @return bool|null
     */
    public function restore(Order $order): ?bool
    {
        return $order->restore();
    }

    /**
     * Get user orders with optional filters (excludes refundable orders)
     *
     * @param User $user
     * @param array $filters ['status' => string, 'sort_by' => 'latest'|'oldest']
     * @return Collection
     */
    public function getUserOrders(User $user, array $filters = []): Collection
    {
        $query = $user->orders()
            ->where(function($q) {
                // Include non-refund orders OR partially-refunded orders
                $q->where('refundable', false)
                  ->orWhereExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('order_items')
                          ->whereColumn('order_items.order_id', 'orders.id')
                          ->where('order_items.request_refund', false);
                  });
            });

        // Apply status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'cancelled') {
                // When filtering by cancelled, include related states
                $query->whereIn('status', ['cancelled', 'request_rejected', 'request_cancel']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'latest';
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * Get user refundable orders with optional filters
     *
     * @param User $user
     * @param array $filters ['status' => string, 'sort_by' => 'latest'|'oldest']
     * @return Collection
     */
    public function getUserRefundableOrders(User $user, array $filters = []): Collection
    {
        $query = $user->orders()
            ->where(function($q){
                $q->where('refundable', true)
                  ->orWhere('refund_status', 'request_rejected');
            });

        // Load items with refund flag (kept to show requested items)
        $query->with([
            'items' => function($q) {
                $q->where('request_refund', true);
            },
            'items.product'
        ]);

        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'latest';
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    /**
     * Get single user order with relationships (non-refundable orders only)
     *
     * @param User $user
     * @param int $orderId
     * @return Order|null
     */
    public function getUserOrder(User $user, int $orderId): ?Order
    {
        return $user->orders()
            ->with([
                'items.product',
                'items.weightOption',
                'items.cuttingOption',
                'items.packagingOption',
                'address',
                'problem',
                'coupon',
                'cancelReason',
                'refundOrder',
                'refundReason'
            ])
            ->find($orderId);
    }

    /**
     * Get single user refundable order with relationships
     *
     * @param User $user
     * @param int $orderId
     * @return Order|null
     */
    public function getUserRefundableOrder(User $user, int $orderId): ?Order
    {
        return $user->orders()
            ->where(function($q){
                $q->where('refundable', true)
                  ->orWhere('refund_status', 'request_rejected');
            })
            ->with([
                'items.product',
                'items.weightOption',
                'items.cuttingOption',
                'items.packagingOption',
                'address',
                'address.city',
                'address.district',
                'city',  // For gift orders
                'delivery',
                'refundReason',
                'user'
            ])
            ->find($orderId);
    }

    /**
     * Get delivery person's orders
     *
     * @param int $deliveryUserId
     * @param string|null $status
     * @param string|null $search
     * @return Collection
     */
    public function getDeliveryOrders(int $deliveryUserId, ?string $status, ?string $search): Collection
    {
        $isRefundStatus = $status && in_array($status, ['request_refund', 'refunded']);
        
        $query = Order::with([
            'user',
            'items' => function($q) use ($isRefundStatus) {
                // Only load refund items if filtering by refund status
                if ($isRefundStatus) {
                    $q->where('request_refund', true);
                }
            },
            'items.product'
        ])
            ->where('delivery_id', $deliveryUserId)
            ->when($status, function ($q) use ($status, $isRefundStatus) {
                // For refund-related statuses, use refund_status and filter by refundable flag
                if ($isRefundStatus) {
                    $q->where('refund_status', $status)->where('refundable', true);
                } else {
                    // Exclude refund orders for non-refund statuses
                    $q->where('status', $status)->where('refundable', false);
                }
            }, function ($q) {
                // Include pending orders and refund orders for delivery users
                $q->where(function($subQ) {
                    // Default list should exclude refund orders
                    $subQ->whereIn('status', ['pending', 'new', 'delivered', 'confirmed', 'problem'])
                        ->where('refundable', false);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', fn($q2) => $q2->where('name', 'LIKE', "%{$search}%"));
                });
            })
            ->latest();
            
        return $query->get();
    }

    /**
     * Get single delivery order
     *
     * @param int $deliveryUserId
     * @param int $orderId
     * @return Order|null
     */
    public function getDeliveryOrder(int $deliveryUserId, int $orderId): ?Order
    {
        return Order::with([
                'user',
                'items.product',
                'address',
                'address.city',
                'address.district',
                'city', // For gift orders without address
            ])
            ->whereIn('status', ['pending', 'new', 'delivered', 'confirmed', 'problem'])
            ->where('delivery_id', $deliveryUserId)
            ->find($orderId);
    }

    /**
     * Attach coupon to order
     *
     * @param Order $order
     * @param int $couponId
     * @return bool
     */
    public function attachCoupon(Order $order, int $couponId): bool
    {
        return $order->update(['coupon_id' => $couponId]);
    }

    /**
     * Increment user wallet balance
     *
     * @param User $user
     * @param float $amount
     * @return int
     */
    public function incrementUserWallet(User $user, float $amount): int
    {
        return $user->increment('wallet_balance', $amount);
    }

    /**
     * Decrement user wallet balance
     *
     * @param User $user
     * @param float $amount
     * @return int
     */
    public function decrementUserWallet(User $user, float $amount): int
    {
        return $user->decrement('wallet_balance', $amount);
    }

    /**
     * Execute a database transaction
     *
     * @param callable $callback
     * @return mixed
     */
    public function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Check if order exists by ID
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return Order::where('id', $id)->exists();
    }

    /**
     * Get orders by status
     *
     * @param string $status
     * @return Collection
     */
    public function getByStatus(string $status): Collection
    {
        return Order::where('status', $status)->get();
    }

    /**
     * Get orders by payment status
     *
     * @param string $paymentStatus
     * @return Collection
     */
    public function getByPaymentStatus(string $paymentStatus): Collection
    {
        return Order::where('payment_status', $paymentStatus)->get();
    }

    /**
     * Get orders by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return Order::where('user_id', $userId)->get();
    }
}

