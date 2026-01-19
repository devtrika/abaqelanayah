<?php

namespace App\Services\Order;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

/**
 * InventoryService
 *
 * Handles all inventory and stock management operations
 */
class InventoryService
{
    public function __construct()
    {
    }

    /**
     * Deduct product quantities when order is created
     *
     * @param Order $order
     * @throws \Exception
     */
    public function deductStock(Order $order): void
    {
        foreach ($order->items as $orderItem) {
            if (!$orderItem->product_id) {
                continue;
            }

            $this->deductGlobalStock($orderItem);
        }
    }

    /**
     * Deduct stock from global inventory
     *
     * @param \App\Models\OrderItem $orderItem
     * @throws \Exception
     */
    private function deductGlobalStock($orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product && $product->quantity >= $orderItem->quantity) {
            $product->decrement('quantity', $orderItem->quantity);
        } else {
            $available = $product ? $product->quantity : 0;
            throw new \Exception($this->buildInsufficientStockMessage(
                $orderItem,
                $available,
                'global'
            ));
        }
    }

    /**
     * Restore product quantities when order is cancelled
     *
     * @param Order $order
     */
    public function restoreStock(Order $order): void
    {
        foreach ($order->items as $orderItem) {
            if (!$orderItem->product_id) {
                continue;
            }

            $this->restoreGlobalStock($orderItem);
        }

        Log::info('Product quantities restored for order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Restore stock to global inventory
     *
     * @param \App\Models\OrderItem $orderItem
     */
   

    /**
     * Restore stock to global inventory
     *
     * @param \App\Models\OrderItem $orderItem
     */
    private function restoreGlobalStock($orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product) {
            $product->increment('quantity', $orderItem->quantity);
        }
    }

    /**
     * Build detailed insufficient stock error message
     *
     * @param \App\Models\OrderItem $orderItem
     * @param int $available
     * @return string
     */
    private function buildInsufficientStockMessage($orderItem, int $available): string
    {
        $productName = optional($orderItem->product)->name ?? __('apis.product') . ' #' . $orderItem->product_id;
        
        $messageKey = 'apis.insufficient_stock';

        return sprintf(
            '%s: %s (%s: %d, %s: %d)',
            __($messageKey),
            $productName,
            __('apis.requested'),
            $orderItem->quantity,
            __('apis.available'),
            $available
        );
    }
}

