<?php

namespace App\Services\Order;

use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Repositories\AddressRepository;
use App\Repositories\CouponRepository;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * OrderCheckoutService
 * 
 * Orchestrates the entire checkout flow from cart to order
 * This is the main service that coordinates all other order-related services
 */
class OrderCheckoutService
{
    protected $orderRepository;
    protected $cartRepository;
    protected $addressRepository;
    protected $couponRepository;
    protected $cartService;
    protected $orderCreationService;
    protected $orderValidationService;
    protected $orderPaymentService;
    protected $deliveryCalculationService;
    protected $inventoryService;
    protected $orderNotificationService;

    public function __construct(
        OrderRepository $orderRepository,
        CartRepository $cartRepository,
        AddressRepository $addressRepository,
        CouponRepository $couponRepository,
        CartService $cartService,
        OrderCreationService $orderCreationService,
        OrderValidationService $orderValidationService,
        OrderPaymentService $orderPaymentService,
        DeliveryCalculationService $deliveryCalculationService,
        InventoryService $inventoryService,
        OrderNotificationService $orderNotificationService
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->addressRepository = $addressRepository;
        $this->couponRepository = $couponRepository;
        $this->cartService = $cartService;
        $this->orderCreationService = $orderCreationService;
        $this->orderValidationService = $orderValidationService;
        $this->orderPaymentService = $orderPaymentService;
        $this->deliveryCalculationService = $deliveryCalculationService;
        $this->inventoryService = $inventoryService;
        $this->orderNotificationService = $orderNotificationService;
    }

    /**
     * Create order from cart (main checkout flow)
     *
     * @param User $user
     * @param array $data
     * @param array $options - Optional parameters like callback_url and error_url for API
     * @return array ['order' => Order, 'payment_url' => string|null]
     * @throws \Exception
     */
    public function createOrderFromCart(User $user, array $data, array $options = []): array
    {
        return DB::transaction(function () use ($user, $data, $options) {
            // Step 1: Validate cart and address
            $validated = $this->orderValidationService->validateCartAndAddress($user, $data);
            $cart = $validated['cart'];
            $address = $validated['address'];

            // Step 2: Validate order type and delivery type
            $this->orderValidationService->validateOrderType($data);
            $this->orderValidationService->validateDeliveryType($data);

            // Step 3: Calculate delivery details (branch, distance, fee)
            $orderType = $data['order_type'] ?? 'regular';
            $deliveryFee = ($orderType === 'pickup') ? 0 : (float) getSiteSetting('delivery_fee', 15);

            $deliveryDetails = [
                'delivery_fee' => $deliveryFee,
                'branch_id' => null,
                'distance' => 0,
            ];

            // Step 4: Validate branch stock availability
            $orderType = $data['order_type'] ?? 'regular';

            // Step 4.5: Create an address if not provided but coordinates exist (ordinary orders only)
            if ($orderType !== 'gift' && empty($data['address_id'])) {
                $lat = $data['latitude'] ?? $data['lat'] ?? null;
                $lng = $data['longitude'] ?? $data['lng'] ?? null;
                if ($lat !== null && $lng !== null) {
                    $address = $this->addressRepository->create([
                        'user_id' => $user->id,
                        'address_name' => $data['address_name'] ?? null,
                        'recipient_name' => $data['recipient_name'] ?? $user->name,
                        'city_id' => $data['city_id'] ?? null,
                        'districts_id' => $data['districts_id'] ?? null,
                        'latitude' => (float) $lat,
                        'longitude' => (float) $lng,
                        'phone' => $data['phone'] ?? $user->phone,
                        'country_code' => $data['country_code'] ?? $user->country_code,
                        'description' => $data['description'] ?? null,
                        'is_default' => false,
                    ]);
                    // Attach the newly created address to the request data for downstream services
                    $data['address_id'] = $address->id;
                }
            }

            // Step 5: Prepare order data
            $orderData = $this->prepareOrderData($user, $cart, $data, $deliveryDetails, $address);

            // Step 6: Create order and order items
            $order = $this->orderCreationService->createFromCart($cart, $orderData);

            // Step 7: Deduct inventory stock
            $this->inventoryService->deductStock($order);

            // Step 8: Handle coupon usage if applied
            if ($cart->coupon_id) {
                $this->handleCouponUsage($cart->coupon_id, $order);
            }

            // Step 9: Clear user cart and cleanup
            $this->clearUserCart($user);

            // Step 10: Process payment (pass options for API callback URLs)
            $paymentResult = $this->orderPaymentService->processPayment($order, $data, $options);

            // Step 11: Send notifications
            $this->orderNotificationService->notifyAdminsOfNewOrder($order);

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $user->id,
                'total' => $order->total,
            ]);

            return [
                'order' => $order->fresh(['items.product', 'address']),
                'payment_url' => $paymentResult['payment_url'],
            ];
        });
    }

    /**
     * Prepare order data from cart and request data
     *
     * @param User $user
     * @param \App\Models\Cart $cart
     * @param array $data
     * @param array $deliveryDetails
     * @param \App\Models\Address|null $address
     * @return array
     */
    private function prepareOrderData(User $user, $cart, array $data, array $deliveryDetails, $address): array
    {
        $orderType = $data['order_type'] ?? 'regular';
        $deliveryType = $data['delivery_type'] ?? 'immediate';

        // Use cart's already calculated values (cart already has VAT calculated)
        $subtotal = $cart->subtotal;
        $discountAmount = $cart->discount ?? 0;
        $couponAmount = $cart->coupon_value ?? 0;
        $deliveryFee = $deliveryDetails['delivery_fee'];
        $walletDeduction = $cart->wallet_deduction ?? 0;
        $vatAmount = $cart->vat_amount ?? 0;

        // Gift fee when order type is gift
        $giftFee = $orderType === 'gift' ? (float) getSiteSetting('gift_fee', 0) : 0;
        // Total = cart total + delivery fee + gift fee
        // Cart total already includes: subtotal - discounts + VAT (before delivery fee)
        $total = $cart->total + $deliveryFee + $giftFee;

        $orderData = [
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'payment_method_id' => $data['payment_method_id'],
            'payment_status' => 'pending',
            'delivery_type' => $deliveryType,
            'order_type' => $orderType,
            'address_id' => $address?->id,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_code' => $cart->coupon_code ?? null,
            'coupon_id' => null, // Will be set later in handleCouponUsage
            'coupon_amount' => $couponAmount,
            'delivery_fee' => $deliveryFee,
            'gift_fee' => $giftFee,
            'wallet_deduction' => $walletDeduction,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ];

        // Add gift order specific fields
        if ($orderType === 'gift') {
            $orderData = array_merge($orderData, [
                'reciver_name' => $data['reciver_name'] ?? null,
                'reciver_phone' => $data['reciver_phone'] ?? null,
                'gift_address_name' => $data['gift_address_name'] ?? null,
                'gift_city_id' => $data['gift_city_id'] ?? null,
                'gift_districts_id' => $data['gift_districts_id'] ?? null,
                'gift_latitude' => isset($data['gift_latitude']) ? (float) $data['gift_latitude'] : (isset($data['gift_lat']) ? (float) $data['gift_lat'] : null),
                'gift_longitude' => isset($data['gift_longitude']) ? (float) $data['gift_longitude'] : (isset($data['gift_lng']) ? (float) $data['gift_lng'] : null),
                'message' => $data['message'] ?? null,
                'whatsapp' => isset($data['whatsapp']) ? (int) $data['whatsapp'] : 0,
                'hide_sender' => isset($data['hide_sender']) ? (int) $data['hide_sender'] : 0,
            ]);
        }

        // Add schedule fields if present
        if (isset($data['schedule_date'])) {
            $orderData['schedule_date'] = $data['schedule_date'];
        }
        if (isset($data['schedule_time'])) {
            $orderData['schedule_time'] = $data['schedule_time'];
        }

        return $orderData;
    }

    /**
     * Generate unique order number
     *
     * @return string
     */
    private function generateOrderNumber(): string
    {
        return  rand(100000, 999900);
    }

    /**
     * Handle coupon usage after order creation
     *
     * @param int $couponId
     * @param \App\Models\Order $order
     */
    private function handleCouponUsage(int $couponId, $order): void
    {
        $coupon = $this->couponRepository->find($couponId);

        if ($coupon) {
            // Attach coupon to order
            $this->orderRepository->attachCoupon($order, $couponId);

            // Increment usage count and decrement usage time
            $this->couponRepository->incrementUsage($coupon);
            $this->couponRepository->decrementUsageTime($coupon);

            Log::info('Coupon applied to order', [
                'order_id' => $order->id,
                'coupon_id' => $couponId,
                'coupon_code' => $coupon->coupon_num,
            ]);
        }
    }

    /**
     * Clear user cart after successful order creation
     * Also cleanup wallet deductions and coupon applications
     *
     * @param User $user
     */
    private function clearUserCart(User $user): void
    {
        $cart = $this->cartRepository->getCart($user);

        if ($cart) {
            // Clear cart items and reset all cart fields
            // clearCart already handles: items deletion, subtotal, total, discount, coupon_code,
            // coupon_value, loyalty_points_used, loyalty_points_value, wallet_deduction, vat_amount
            $this->cartRepository->clearCart($cart);

            Log::info('Cart cleared after order creation', [
                'user_id' => $user->id,
            ]);
        }
    }
}

