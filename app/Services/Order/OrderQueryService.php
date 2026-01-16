<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;

/**
 * OrderQueryService
 *
 * Handles all order retrieval and query operations
 * Uses repositories for all database operations
 */
class OrderQueryService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Get user orders with filters (excludes refundable orders)
     *
     * @param User $user
     * @param array $filters ['status' => string, 'sort_by' => 'latest'|'oldest']
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserOrders(User $user, array $filters = [])
    {
        return $this->orderRepository->getUserOrders($user, $filters);
    }

    /**
     * Get user refundable orders with filters
     *
     * @param User $user
     * @param array $filters ['status' => string, 'sort_by' => 'latest'|'oldest']
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserRefundableOrders(User $user, array $filters = [])
    {
        return $this->orderRepository->getUserRefundableOrders($user, $filters);
    }

    /**
     * Get single user order with relationships (non-refundable only)
     *
     * @param User $user
     * @param int $orderId
     * @return Order|null
     */
    public function getUserOrder(User $user, int $orderId)
    {
        return $this->orderRepository->getUserOrder($user, $orderId);
    }

    /**
     * Get single user refundable order with relationships
     *
     * @param User $user
     * @param int $orderId
     * @return Order|null
     */
    public function getUserRefundableOrder(User $user, int $orderId)
    {
        return $this->orderRepository->getUserRefundableOrder($user, $orderId);
    }

    /**
     * Get delivery person's orders
     *
     * @param string|null $status
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDeliveryOrders(?string $status, ?string $search)
    {
        $user = auth()->user();
        return $this->orderRepository->getDeliveryOrders($user->id, $status, $search);
    }

    /**
     * Get single delivery order
     *
     * @param int $id
     * @return Order|null
     */
    public function getDeliveryOrder(int $id)
    {
        $user = auth()->user();

        if ($user->type !== 'delivery') {
            return null;
        }

        return $this->orderRepository->getDeliveryOrder($user->id, $id);

    }
}

