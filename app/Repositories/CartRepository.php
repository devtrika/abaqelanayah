<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    /**
     * Get or create a cart for a user
     *
     * @param User $user
     * @return Cart
     */
    public function getCart($user)
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Find or create a cart item with the same product and options
     *
     * @param Cart $cart
     * @param array $data
     * @return CartItem
     */
    public function findOrCreateCartItem(Cart $cart, array $data)
    {
        $item = $cart->items()->where([
            'product_id' => $data['product_id'],
            'weight_option_id' => $data['weight_option_id'] ?? null,
            'cutting_option_id' => $data['cutting_option_id'] ?? null,
            'packaging_option_id' => $data['packaging_option_id'] ?? null,
        ])->first();

        $product = Product::find($data['product_id']);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        $requestedQuantity = (int) $data['quantity'];

        // Determine available quantity from product (uses accessor if exists)
        $availableQuantity = $product->available_quantity ?? $product->quantity ?? 0;

        // Calculate the resulting quantity after adding
        $resultQuantity = $requestedQuantity + ($item ? $item->quantity : 0);

        // Validate requested quantity against available stock
        // if ($availableQuantity !== null && $resultQuantity > $availableQuantity) {
        //     throw new \Exception(__('cart.requested_quantity_exceeds_available'));
        // }

        if ($item) {
            $item->quantity += $requestedQuantity;
            $item->save();
            return $item;
        } else {
            return $cart->items()->create([
                'product_id' => $data['product_id'],
            'quantity' => $requestedQuantity,
                'weight_option_id' => $data['weight_option_id'] ?? null,
                'cutting_option_id' => $data['cutting_option_id'] ?? null,
                'packaging_option_id' => $data['packaging_option_id'] ?? null,
                'price' => 0,
                'discount_amount' => 0,
                'total' => 0,
            ]);
        }
    }

    /**
     * Update a cart item
     *
     * @param Cart $cart
     * @param array $data
     * @return CartItem|null
     */
    public function updateCartItem(Cart $cart, array $data)
    {
        // Support both cart_item_id and product_id for backward compatibility
        if (isset($data['cart_item_id'])) {
            $item = $cart->items()->where('id', $data['cart_item_id'])->first();
        } elseif (isset($data['product_id'])) {
            $item = $cart->items()->where('product_id', $data['product_id'])->first();
        } else {
            return null;
        }

        if ($item) {
            $item->update([
                'quantity' => $data['quantity'],
                'weight_option_id' => $data['weight_option_id'] ?? $item->weight_option_id,
                'cutting_option_id' => $data['cutting_option_id'] ?? $item->cutting_option_id,
                'packaging_option_id' => $data['packaging_option_id'] ?? $item->packaging_option_id,
            ]);
        }

        return $item;
    }

    /**
     * Remove an item from the cart
     *
     * @param Cart $cart
     * @param int $itemId - The cart_item ID (not product_id)
     * @return bool
     */
    public function removeFromCart(Cart $cart, int $itemId)
    {
        return (bool) $cart->items()->where('product_id', $itemId)->delete();
    }


    /**
     * Clear all items from the cart
     *
     * @param Cart $cart
     * @return bool
     */
    public function clearCart(Cart $cart)
    {
        $cart->items()->delete();
        
        return $cart->update([
            'subtotal' => 0,
            'total' => 0,
            'discount' => 0,
            'coupon_code' => null,
            'coupon_value' => null,
            'loyalty_points_used' => 0,
            'loyalty_points_value' => null,
            'vat_amount' => 0,
            'wallet_deduction' => 0,
        ]);
    }

    /**
     * Find a coupon by code
     *
     * @param string $code
     * @return Coupon
     */
    public function findCouponByCode(string $code)
    {
        return Coupon::where('coupon_num', $code)->firstOrFail();
    }

    /**
     * Update cart with coupon information
     *
     * @param Cart $cart
     * @param string $couponCode
     * @param float $couponValue
     * @return bool
     */
    public function applyCouponToCart(Cart $cart, string $couponCode, float $couponValue)
    {
        return $cart->update([
            'coupon_code' => $couponCode,
            'coupon_value' => $couponValue,
        ]);
    }

    /**
     * Remove coupon from cart
     *
     * @param Cart $cart
     * @return bool
     */
    public function removeCouponFromCart(Cart $cart)
    {
        return $cart->update([
            'coupon_code' => null,
            'coupon_value' => null,
        ]);
    }

    /**
     * Apply loyalty points to cart
     *
     * @param Cart $cart
     * @param int $points
     * @param float $pointsValue
     * @return bool
     */
    public function applyLoyaltyPointsToCart(Cart $cart, int $points, float $pointsValue)
    {
        return $cart->update([
            'loyalty_points_used' => $points,
            'loyalty_points_value' => $pointsValue
        ]);
    }

    /**
     * Apply wallet deduction to cart
     *
     * @param Cart $cart
     * @param int $amount
     * @return bool
     */
    public function applyWalletDeductionToCart(Cart $cart, int $amount)
    {
        return $cart->update(['wallet_deduction' => $amount]);
    }

    /**
     * Decrement user's wallet balance
     *
     * @param User $user
     * @param int $amount
     * @return bool
     */
    public function decrementUserWalletBalance(User $user, int $amount)
    {
        return $user->decrement('wallet_balance', $amount);
    }

    /**
     * Get app settings
     *
     * @return array
     */
    public function getAppSettings()
    {
        return SiteSetting::pluck('value', 'key')->toArray();
    }

    /**
     * Update cart totals
     *
     * @param Cart $cart
     * @param float $subtotal
     * @param float $discount
     * @param float $total
     * @param float $vatAmount
     * @return bool
     */
    public function updateCartTotals(Cart $cart, float $subtotal, float $discount, float $total, float $vatAmount)
    {
        return $cart->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max($total, 0),
            'vat_amount' => $vatAmount,
        ]);
    }

    /**
     * Update cart item totals
     *
     * @param CartItem $item
     * @param float $price
     * @param float $discountAmount
     * @param float $total
     * @return bool
     */
    public function updateCartItemTotals(CartItem $item, float $price, float $discountAmount, float $total)
    {
        return $item->update([
            'price' => $price,
            'discount_amount' => $discountAmount,
            'total' => $total
        ]);
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
}
