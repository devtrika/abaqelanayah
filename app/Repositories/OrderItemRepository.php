<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

/**
 * OrderItemRepository
 * 
 * Handles all database operations for order items
 */
class OrderItemRepository
{
    /**
     * Create order item
     *
     * @param Order $order
     * @param array $data
     * @return OrderItem
     */
    public function create(Order $order, array $data): OrderItem
    {
        return $order->items()->create($data);
    }

    /**
     * Create multiple order items
     *
     * @param Order $order
     * @param array $items
     * @return bool
     */
    public function createMany(Order $order, array $items): bool
    {
        foreach ($items as $itemData) {
            $order->items()->create($itemData);
        }
        
        return true;
    }

    /**
     * Find order item by ID
     *
     * @param int $id
     * @return OrderItem|null
     */
    public function find(int $id): ?OrderItem
    {
        return OrderItem::find($id);
    }

    /**
     * Update order item
     *
     * @param OrderItem $orderItem
     * @param array $data
     * @return bool
     */
    public function update(OrderItem $orderItem, array $data): bool
    {
        return $orderItem->update($data);
    }

    /**
     * Delete order item
     *
     * @param OrderItem $orderItem
     * @return bool|null
     */
    public function delete(OrderItem $orderItem): ?bool
    {
        return $orderItem->delete();
    }

    /**
     * Get all items for an order
     *
     * @param Order $order
     * @return Collection
     */
    public function getOrderItems(Order $order): Collection
    {
        return $order->items()->with([
            'product',
            'weightOption',
            'cuttingOption',
            'packagingOption'
        ])->get();
    }

    /**
     * Get order items by product ID
     *
     * @param Order $order
     * @param int $productId
     * @return Collection
     */
    public function getByProductId(Order $order, int $productId): Collection
    {
        return $order->items()->where('product_id', $productId)->get();
    }

    /**
     * Calculate total quantity for an order
     *
     * @param Order $order
     * @return int
     */
    public function getTotalQuantity(Order $order): int
    {
        return $order->items()->sum('quantity');
    }

    /**
     * Calculate total amount for an order
     *
     * @param Order $order
     * @return float
     */
    public function getTotalAmount(Order $order): float
    {
        return $order->items()->sum('total');
    }
}

