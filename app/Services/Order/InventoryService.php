<?php

namespace App\Services\Order;

use App\Models\BranchProduct;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\BranchRepository;
use Illuminate\Support\Facades\Log;

/**
 * InventoryService
 *
 * Handles all inventory and stock management operations
 * Uses repositories for all database operations
 */
class InventoryService
{
    protected $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }
    /**
     * Validate branch-level stock for all cart items before creating the order
     *
     * @param Cart $cart
     * @param int|null $branchId
     * @param string|null $orderType
     * @throws \Exception
     */
    public function validateBranchStock(Cart $cart, ?int $branchId, ?string $orderType = null): void
    {
        // Skip branch stock validation for gift orders or when branch is not determined
        if ($orderType === 'gift' || empty($branchId)) {
            return;
        }

        $insufficient = [];

        foreach ($cart->items as $item) {
            if (!$item->product_id) {
                continue;
            }

            $branchProduct = BranchProduct::where('branch_id', $branchId)
                ->where('product_id', $item->product_id)
                ->first();

            $available = $branchProduct?->qty ?? 0;

            if ($available < $item->quantity) {
                $productName = optional($item->product)->name ?? __('apis.product') . ' #' . $item->product_id;
                $insufficient[] = sprintf(
                    '%s (%s: %d, %s: %d)',
                    $productName,
                    __('apis.requested'),
                    $item->quantity,
                    __('apis.available'),
                    $available
                );
            }
        }

        if (!empty($insufficient)) {
            $message = __('apis.insufficient_stock_in_branch') . ': ' . implode(' | ', $insufficient);
            throw new \Exception($message);
        }
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

            // Prefer branch-level stock deduction when branch_id is present
            if (!empty($order->branch_id)) {
                $this->deductBranchStock($order->branch_id, $orderItem);
            } else {
                $this->deductGlobalStock($orderItem);
            }
        }
    }

    /**
     * Deduct stock from branch inventory
     *
     * @param int $branchId
     * @param \App\Models\OrderItem $orderItem
     * @throws \Exception
     */
    private function deductBranchStock(int $branchId, $orderItem): void
    {
        $branchProduct = BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $orderItem->product_id)
            ->first();

        if ($branchProduct) {
            if ($branchProduct->qty >= $orderItem->quantity) {
                $branchProduct->decrement('qty', $orderItem->quantity);
            } else {
                throw new \Exception($this->buildInsufficientStockMessage(
                    $orderItem,
                    $branchProduct->qty,
                    'branch'
                ));
            }
        } else {
            throw new \Exception($this->buildInsufficientStockMessage(
                $orderItem,
                0,
                'branch'
            ));
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

            // Restore branch-level stock if branch_id is present
            if (!empty($order->branch_id)) {
                $this->restoreBranchStock($order->branch_id, $orderItem);
            } else {
                $this->restoreGlobalStock($orderItem);
            }
        }

        Log::info('Product quantities restored for order', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Restore stock to branch inventory
     *
     * @param int $branchId
     * @param \App\Models\OrderItem $orderItem
     */
    private function restoreBranchStock(int $branchId, $orderItem): void
    {
        $branchProduct = BranchProduct::where('branch_id', $branchId)
            ->where('product_id', $orderItem->product_id)
            ->first();

        if ($branchProduct) {
            $branchProduct->increment('qty', $orderItem->quantity);
        }
    }

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
     * @param string $type 'branch' or 'global'
     * @return string
     */
    private function buildInsufficientStockMessage($orderItem, int $available, string $type): string
    {
        $productName = optional($orderItem->product)->name ?? __('apis.product') . ' #' . $orderItem->product_id;
        
        $messageKey = $type === 'branch' 
            ? 'apis.insufficient_stock_in_branch' 
            : 'apis.insufficient_stock';

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

