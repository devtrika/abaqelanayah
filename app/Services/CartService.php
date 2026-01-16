<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use App\Models\Coupon;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Create a new CartService instance.
     *
     * @param CartRepository $repository
     * @param TransactionService $transactionService
     */
    public function __construct(
        protected CartRepository $repository,
        protected TransactionService $transactionService
    ) {
    }
    /**
     * Get or create a cart for a user
     *
     * @param User $user
     * @return Cart
     */
    public function getCart($user)
    {
        return $this->repository->getCart($user);
    }

    /**
     * Add an item to the cart
     *
     * @param User $user
     * @param array $data
     * @return Cart
     */
    public function addToCart($user, $data)
    {
        $cart = $this->getCart($user);
        
        $this->repository->findOrCreateCartItem($cart, $data);
        $this->updateCartItemsTotals($cart);
        $this->calculateTotals($cart);
        
        return $cart->fresh('items.product');
    }

    /**
     * Update a cart item
     *
     * @param User $user
     * @param array $data
     * @return Cart
     */
    public function updateCartItem($user, $data)
    {
        $cart = $this->getCart($user);
        
        $this->repository->updateCartItem($cart, $data);
        
        $this->updateCartItemsTotals($cart);
        $this->calculateTotals($cart);
        
        return $cart->fresh('items.product');
    }

    /**
     * Remove an item from the cart
     *
     * @param User $user
     * @param int $itemId
     * @return Cart
     */
    public function removeFromCart($user, $itemId)
    {
        $cart = $this->getCart($user);

        $this->repository->removeFromCart($cart, $itemId);

        // Refresh cart to get updated items count
        $cart->refresh();

        // If cart is now empty, reset all values (coupon, wallet, discounts, VAT)
        if ($cart->items->count() === 0) {
            // Refund wallet deduction if any
            $walletAmount = (int) ($cart->wallet_deduction ?? 0);
            if ($walletAmount > 0) {
                $user->increment('wallet_balance', $walletAmount);
            }

            // Reset all cart values
            $cart->update([
                'subtotal' => 0,
                'total' => 0,
                'discount' => 0,
                'coupon_code' => null,
                'coupon_value' => 0,
                'wallet_deduction' => 0,
                'vat_amount' => 0,
            ]);
        } else {
            // Cart still has items, recalculate totals normally
            $this->updateCartItemsTotals($cart);
            $this->calculateTotals($cart);
        }

        return $cart->fresh('items.product');
    }

    /**
     * Clear all items from the cart
     *
     * @param User $user
     * @return bool
     */
    public function clearCart($user)
    {
        $cart = $this->getCart($user);
        return $this->repository->clearCart($cart);
    }

    /**
     * Apply a coupon to the cart
     *
     * @param User $user
     * @param string $code
     * @return Cart
     */
    public function applyCoupon(User $user, $code)
    {
        $cart = $this->getCart($user);
        $this->updateCartItemsTotals($cart); // important before calculating subtotal

        // Check if a coupon is already applied
        if (!empty($cart->coupon_code)) {
            // If trying to apply the same coupon, just return success
            if ($cart->coupon_code === $code) {
                return $cart->fresh('items.product');
            }
            // If trying to apply a different coupon, remove the existing one first
            $this->repository->removeCouponFromCart($cart);
        }

        $coupon = $this->repository->findCouponByCode($code);

        // Enforce one-time use per client (allow reuse only if previous order was cancelled/request_cancel)
        $alreadyUsedByUser = \App\Models\Order::where('user_id', $user->id)
            ->where(function ($q) use ($coupon) {
                $q->where('coupon_id', $coupon->id)
                  ->orWhere('discount_code', $coupon->coupon_num);
            })
            ->whereNotIn('status', [\App\Enums\OrderStatus::CANCELLED->value, \App\Enums\OrderStatus::REQUEST_CANCEL->value])
            ->exists();

        if ($alreadyUsedByUser) {
            throw new \Exception(__('cart.coupon_already_used'));
        }

        // If coupon exists but is not active, treat it as not found for users
        if (isset($coupon) && isset($coupon->is_active) && !$coupon->is_active) {
            throw new \Exception(__('cart.coupon_not_found'));
        }

        // Validate coupon status and expiry
        if ($coupon->status === 'closed') {
            throw new \Exception(__('cart.coupon_not_available'));
        }
        
        if ($coupon->status === 'usage_end' || $coupon->usage_time == 0) {
            throw new \Exception(__('cart.coupon_usage_ended'));
        }
        
        if ($coupon->expire_date < today() || $coupon->status === 'expire') {
            throw new \Exception(__('cart.coupon_expired'));
        }
        
        if ($coupon->start_date > today()) {
            throw new \Exception(__('cart.coupon_not_started'));
        }
        
        $couponValue = $this->calculateCouponDiscount($cart, $coupon);

        // use coupon_num (stored field) as the coupon code saved on cart
        $couponCode = $coupon->coupon_num ?? null;
        if (!is_string($couponCode) || $couponCode === '') {
            throw new \Exception(__('cart.invalid_coupon_data'));
        }

        $this->repository->applyCouponToCart($cart, $couponCode, $couponValue);
        $this->calculateTotals($cart);
        $coupon->usage_time -= 1;
        $coupon->save();

        return $cart->fresh('items.product');
    }

    /**
     * Apply loyalty points to the cart
     *
     * @param User $user
     * @param int $points
     * @return Cart
     */
    public function applyLoyaltyPoints(User $user, int $points)
    {
        return $this->repository->transaction(function () use ($user, $points) {
            $cart = $this->getCart($user);

            if ($cart->items()->count() === 0) {
                throw new \Exception(__('cart.cart_is_empty'));
            }

            // get loyalty points settings
            $appInfo = $this->repository->getAppSettings();
            $settings = SettingService::appInformations($appInfo);

            // check if loyalty points are enabled
            if (!$settings['loyalty_points_enabled']) {
                throw new \Exception(__('cart.loyalty_points_system_disabled'));
            }

            // check minimum points requirement
            $minRedeem = $settings['loyalty_points_min_redeem'] ?? 10;
            if ($points < $minRedeem) {
                throw new \Exception(__('cart.minimum_points_required', ['points' => $minRedeem]));
            }

            // check if user has enough points
            if ($user->loyalty_points < $points) {
                throw new \Exception(__('cart.insufficient_loyalty_points'));
            }

            // calculate maximum points that can be used (based on percentage)
            $maxPercentage = $settings['loyalty_points_max_redeem_percentage'] ?? 50;
            $cartTotal = $cart->subtotal - $cart->discount_amount;
            $maxPointsValue = ($cartTotal * $maxPercentage) / 100;

            // convert points to value
            $redeemRate = $settings['loyalty_points_redeem_rate'] ?? 1;
            $pointsValue = $points * $redeemRate;

            if ($pointsValue > $maxPointsValue) {
                $maxPoints = floor($maxPointsValue / $redeemRate);
                throw new \Exception(__('cart.cannot_use_more_than_max_points', [
                    'max_points' => $maxPoints,
                    'percentage' => $maxPercentage
                ]));
            }

            $this->repository->applyLoyaltyPointsToCart($cart, $points, $pointsValue);
            $this->updateCartItemsTotals($cart);
            $this->calculateTotals($cart);
            $this->transactionService->createLoyaltyPointsDeductionTransaction($user->id, $points);

            return $cart->load(['items.item']);
        });
    }

    /**
     * Apply wallet deduction to the cart
     *
     * @param User $user
     * @param int $amount
     * @return void
     */
    public function applyWalletDeduction(User $user, int $amount)
    {
        // Validate wallet balance
        $wallet = $user->wallet_balance;
        if ($wallet < $amount) {
            throw new \Exception(__('cart.insufficient_wallet_balance'));
        }

        // Validate amount does not exceed allowed maximum (subtotal minus coupon)
        $cart = $this->getCart($user);
        $this->updateCartItemsTotals($cart); // ensure item totals are up to date
        $subtotal = (float) $cart->items->sum('total');
        $coupon   = (float) ($cart->coupon_value ?? 0);
        $maxAllowed = max(0, $subtotal - $coupon);
        if ($amount > $maxAllowed) {
            throw new \Exception(__('cart.maximum_wallet_deduction_exceeded', ['amount' => number_format($maxAllowed, 2)]));
        }
        
        // Deduct from user wallet and apply to cart
        $this->repository->decrementUserWalletBalance($user, $amount);
        $this->repository->applyWalletDeductionToCart($cart, $amount);
        
        // Recalculate cart totals from server
        $this->updateCartItemsTotals($cart);
        $this->calculateTotals($cart);
        return $cart->fresh('items.product');
    }

    /**
     * Remove coupon from the cart
     *
     * @param User $user
     * @return Cart
     */
    public function removeCoupon($user)
    {
        $cart = $this->getCart($user);

        $this->repository->removeCouponFromCart($cart);
        $this->calculateTotals($cart);

        return $cart->fresh('items.product');
    }

    /**
     * Remove wallet deduction from the cart and refund to user's wallet
     *
     * @param User $user
     * @return Cart
     */
    public function removeWalletDeduction(User $user)
    {
        $cart = $this->getCart($user);
        $walletAmount = (int) ($cart->wallet_deduction ?? 0);

        if ($walletAmount > 0) {
            // Refund the wallet deduction back to user's wallet
            $user->increment('wallet_balance', $walletAmount);

            // Remove wallet deduction from cart
            $cart->update(['wallet_deduction' => 0]);

            // Recalculate cart totals
            $this->updateCartItemsTotals($cart);
            $this->calculateTotals($cart);
        }

        return $cart->fresh('items.product');
    }


    /**
     * Calculate cart totals
     *
     * @param Cart $cart
     * @return void
     */
    public function calculateTotals($cart)
    {
        // do not recalculate items here; only sum and update cart totals
        $subtotal = $cart->items->sum('total');

        $couponDiscount = $cart->coupon_value ?? 0;
        $loyaltyDiscount = $cart->loyalty_points_value ?? 0;
        $walletDiscount = $cart->wallet_deduction ?? 0;
        
    $appSettings = $this->repository->getAppSettings();
    // vat setting may be stored as 15 (meaning 15%) or 0.15 (meaning 15%).
    // Normalize to a fractional rate (e.g. 0.15).
    $vatSetting = isset($appSettings['vat_amount']) ? (float) $appSettings['vat_amount'] : 15.0;
    // if vatSetting > 1 treat it as percent (e.g. 15 -> 0.15). if <= 1 treat it as fraction already (e.g. 0.15).
    $vatRate = $vatSetting > 1 ? ($vatSetting / 100) : $vatSetting;
    $vatRate = max(0, $vatRate); // guard against negative values

    $totalDiscount = $couponDiscount + $walletDiscount;

    // Calculate amount after discounts (before VAT)
    $amountAfterDiscounts = $subtotal - $totalDiscount;

    // VAT is calculated on the total after applying coupon and wallet deductions
    $vatAmount = $amountAfterDiscounts * $vatRate;

    // round monetary values
    $vatAmount = round($vatAmount, 2);
    $total = round($amountAfterDiscounts + $vatAmount, 2);
        
        $this->repository->updateCartTotals($cart, $subtotal, $totalDiscount, $total, $vatAmount);
    }




    /**
     * Calculate coupon discount
     *
     * @param Cart $cart
     * @param Coupon $coupon
     * @return float
     */
    private function calculateCouponDiscount($cart, $coupon)
    {
        $subtotal = $cart->items->sum('total');

        if ($coupon->type === 'ratio') {
            $discount = ($subtotal * $coupon->discount) / 100;

            if ($coupon->max_discount) {
                $discount = min($discount, $coupon->max_discount);
            }
        } elseif ($coupon->type === 'number') {
            $discount = $coupon->discount;
        } else {
            $discount = 0;
        }

        // Ensure discount does not exceed subtotal to prevent negative totals
        $discount = min($discount, $subtotal);

        return round($discount, 2);
    }

    /**
     * Update cart item totals
     *
     * @param Cart $cart
     * @return void
     */
    public function updateCartItemsTotals($cart)
    {
        foreach ($cart->items as $item) {
            $product = $item->product;
            $weightOption = $item->weightOption;
            $cuttingOption = $item->cuttingOption;
            $packagingOption = $item->packagingOption;

            $basePrice = $product->price_after_discount ?? $product->base_price ?? 0;
            $weightPrice = $weightOption ? ($weightOption->additional_price ?? 0) : 0;
            $cuttingPrice = $cuttingOption ? ($cuttingOption->additional_price ?? 0) : 0;
            $packagingPrice = $packagingOption ? ($packagingOption->additional_price ?? 0) : 0;

            // per-unit calculations: include option fees per unit, then multiply by quantity
            $discountPerUnit = ($product->discount_percentage / 100 * $basePrice) ?? 0;
            $optionsPerUnit = $weightPrice + $cuttingPrice + $packagingPrice;

            // unit price shown/stored should include options
            $unitPriceWithOptions = $basePrice + $optionsPerUnit;

            // total = (unit price including options - discount per unit) * quantity
            $total = ($unitPriceWithOptions - $discountPerUnit) * $item->quantity;

            // round for monetary values
            $unitPriceWithOptions = round($unitPriceWithOptions, 2);
            $discountPerUnit = round($discountPerUnit, 2);
            $total = round($total, 2);

            $this->repository->updateCartItemTotals($item, $unitPriceWithOptions, $discountPerUnit, $total);
        }
    }
}
