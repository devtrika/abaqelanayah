<?php

namespace App\Http\Controllers\Api\Client;

use App\Services\Responder;
use App\Services\CartService;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\FeeCalculationService;
use App\Http\Resources\Api\CartResource;
use App\Http\Requests\Api\Cart\AddToCartRequest;
use App\Http\Requests\Api\Cart\ApplyCouponRequest;
use App\Http\Requests\Api\Cart\RemoveFromCartRequest;
use App\Http\Requests\Api\Cart\UpdateCartItemRequest;
use App\Http\Requests\Api\Cart\WalletDeductionRequest;
use App\Http\Requests\Api\Cart\ApplyLoyaltyPointsRequest;

class CartController extends Controller
{
    use ResponseTrait;

    protected $cartService;
    protected $feeCalculationService;

    public function __construct(CartService $cartService, FeeCalculationService $feeCalculationService)
    {
        $this->cartService = $cartService;
        $this->feeCalculationService = $feeCalculationService;
    }

    public function getCart()
    {
        $user = auth()->user();
        $cart = $this->cartService->getCart($user);

        if (!$cart->items()->count() > 0) {
            return Responder::success([], ['message' => __('apis.cart_is_empty')]);
        }

        $cart->load('items.product');
        return Responder::success(new CartResource($cart));
    }

    public function addToCart(AddToCartRequest $request)
    {


        try {
            $user = auth()->user();
            $data = $request->validated();
            // Optionally fetch price/discount from product/options logic here
            $result = $this->cartService->addToCart($user, $data);
            if (is_array($result) && isset($result['requires_confirmation'])) {
                return Responder::success([], $result['message'], 408);
            }

            return Responder::success(new CartResource($result), ['message' => __('apis.product_added_to_cart')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function updateCartItem(UpdateCartItemRequest $request)
    {

        try {
            $user = auth()->user();
            $data = $request->validated();
            $cart = $this->cartService->updateCartItem($user, $data);

            return Responder::success(new CartResource($cart), ['message' => __('apis.cart_updated')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function removeFromCart(RemoveFromCartRequest $request)
    {
        try {
            $user = auth()->user();
            $cart = $this->cartService->removeFromCart($user, $request->validated()['product_id']);

            return Responder::success(new CartResource($cart), ['message' => __('apis.product_removed_from_cart')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function clearCart()
    {
        try {
            $user = auth()->user();
            $this->cartService->clearCart($user);

            return Responder::success([], ['message' => __('apis.cart_cleared')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function applyCoupon(ApplyCouponRequest $request)
    {
        try {
            $user = auth()->user();
            $cart = $this->cartService->applyCoupon($user, $request->validated()['coupon_code']);

            return Responder::success(new CartResource($cart), ['message' => __('apis.coupon_applied')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function applyLoyaltyPoints(ApplyLoyaltyPointsRequest $request)
    {
        try {
            $user = auth()->user();
            $cart = $this->cartService->applyLoyaltyPoints($user, $request->validated()['points']);

            return Responder::success(new CartResource($cart), ['message' => __('apis.loyalty_points_applied')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }


    public function applyWalletDeduction(WalletDeductionRequest $request)
    {
        try {
            $user = auth()->user();
            $cart = $this->cartService->ApplyWalletDeduction($user, $request->validated()['amount']);

            return Responder::success(new CartResource($cart), ['message' => __('apis.wallet_deduction_applied')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function removeCoupon()
    {
        try {
            $user = auth()->user();
            $cart = $this->cartService->removeCoupon($user);

            return Responder::success(new CartResource($cart), ['message' => __('apis.coupon_removed')]);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    public function removeLoyaltyPoints()
{
    try {
        $user = auth()->user();
        $cart = $this->cartService->getCart($user);

        $cart->update([
            'loyalty_points_used' => 0,
            'loyalty_points_value' => 0,
        ]);

        $this->cartService->calculateTotals($cart); // ← لازم نعيد الحسبة

        return Responder::success(new CartResource($cart->load(['items.product'])), [
            'message' => __('apis.loyalty_points_removed')
        ]);
    } catch (\Exception $e) {
        return Responder::error($e->getMessage(), [], 422);
    }
}
}
