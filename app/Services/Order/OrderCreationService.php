<?php

namespace App\Services\Order;

use App\Models\Cart;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\OrderItemRepository;

/**
 * OrderCreationService
 *
 * Handles order and order items creation from cart data
 * Uses repositories for all database operations
 */
class OrderCreationService
{
    protected $orderRepository;
    protected $orderItemRepository;

    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Create order from cart with prepared order data
     *
     * @param Cart $cart
     * @param array $orderData
     * @return Order
     */
    public function createFromCart(Cart $cart, array $orderData): Order
    {
        // Create order using repository
        $order = $this->orderRepository->create($orderData);

        // Transfer cart items to order items
        $this->transferCartItems($order, $cart);

        return $order;
    }

    /**
     * Transfer cart items to order items
     *
     * @param Order $order
     * @param Cart $cart
     */
    private function transferCartItems(Order $order, Cart $cart): void
    {
        foreach ($cart->items as $cartItem) {
            $this->orderItemRepository->create($order, [
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'weight_option_id' => $cartItem->weight_option_id ?? null,
                'cutting_option_id' => $cartItem->cutting_option_id ?? null,
                'packaging_option_id' => $cartItem->packaging_option_id ?? null,
                'price' => $cartItem->price,
                'discount_amount' => $cartItem->discount_amount ?? 0,
                'total' => $cartItem->total,
            ]);
        }
    }
}

