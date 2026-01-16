<?php
namespace App\Services;

use App\Enums\PaymentMethod;
use App\Models\Admin;
use App\Models\BranchProduct;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OrderService
{
    protected $cartService;

    protected $paymentService;
    protected $addressService;
    protected $transactionService;

    public function __construct(CartService $cartService, PaymentService $paymentService, AddressService $addressService, TransactionService $transactionService)
    {
        $this->cartService        = $cartService;
        $this->paymentService     = $paymentService;
        $this->addressService     = $addressService;
        $this->transactionService = $transactionService;

    }

    public function getUserOrders(User $user, array $filters = [])
    {
        $query = $user->orders();

        // Apply filters if provided
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sortBy = $filters['sort_by'] ?? 'latest';
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    public function getUserOrder(User $user, int $orderId)
    {
        $order = $user->orders()
            ->with(['items.item', 'address', 'branch', 'problem', 'coupon', 'cancelReason', 'refundOrder'])
            ->find($orderId)
        ;
        if (! $order) {
            return null;
        }

        return $order;
    }

    /**
     * Create an order from the user's cart (checkout)
     *
     * @param \App\Models\User $user
     * @param array $data (validated request)
     * @return Order|array
     */
    public function createOrderFromCart($user, array $data)
    {

        return DB::transaction(function () use ($user, $data) {
            // Step 1: Validate cart and address
            $cart = $this->validateCartAndAddress($user, $data);

            // Step 2: Calculate delivery details
            $deliveryDetails = $this->calculateDeliveryDetails($user, $data);

            // Step 2.5: Validate branch stock for items
            $this->validateBranchQuantities($cart, $deliveryDetails['branch_id'] ?? null, $data['order_type'] ?? null);

            // Step 3: Create order from cart data

            $order = $this->createOrderFromCartData($user, $cart, $data, $deliveryDetails);

            // Step 4: Process payment
            $paymentResult = $this->processOrderPayment($order, $user, $data, $deliveryDetails);

            return $paymentResult;
        });
    }

    public function createAddress($data)
    {

    }

    /**
     * Create an order request with dynamic branch selection based on location
     *
     * @param \App\Models\User $user
     * @param array $data (validated request)
     * @return Order|array
     */
    public function createOrderRequest($user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            // Step 1: Validate cart
            $cart = $this->cartService->getCart($user);
            if (! $cart || $cart->items()->count() === 0) {
                throw new \Exception(__('cart.cart_is_empty'));
            }

            // Step 2: Determine coordinates and branch using dynamic selection
            $locationData = $this->determineLocationAndBranch($user, $data);

            // Step 3: Calculate delivery details with the selected branch (if available)
            if (! empty($locationData['branch'])) {
                $deliveryDetails = $this->calculateDeliveryDetailsForBranch(
                    $locationData['latitude'],
                    $locationData['longitude'],
                    $locationData['branch']
                );
            } else {
                // No branch found (possible for gift orders) -> zero fees and no branch
                $deliveryDetails = [
                    'delivery_fee' => 0,
                    'branch_id'    => null,
                    'distance'     => 0,
                ];
            }

            // Step 4: Create order from cart data
            $order = $this->createOrderFromCartData($user, $cart, array_merge($data, [
                'branch_id'  => $locationData['branch']->id ?? null,
                'address_id' => $locationData['address_id'] ?? null,
            ]), $deliveryDetails);

            // Step 5: Process payment
            $paymentResult = $this->processOrderPayment($order, $user, $data, $deliveryDetails);

            return $paymentResult;
        });
    }
    /**
     * Determine location coordinates and appropriate branch using dynamic selection
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return array ['latitude' => float, 'longitude' => float, 'branch' => Branch, 'address_id' => int|null]
     * @throws \Exception
     */
    private function determineLocationAndBranch($user, array $data)
    {
        $latitude  = null;
        $longitude = null;
        $addressId = null;

        // If this is a gift order, prefer recipient coordinates or recipient address
        if (! empty($data['order_type']) && $data['order_type'] === 'gift') {
            // Use provided gift coordinates first (gift_latitude/gift_longitude or gift_lat/gift_lng)
            if (! empty($data['gift_latitude']) && ! empty($data['gift_longitude'])) {
                $latitude  = (float) $data['gift_latitude'];
                $longitude = (float) $data['gift_longitude'];
            } elseif (! empty($data['gift_lat']) && ! empty($data['gift_lng'])) {
                $latitude  = (float) $data['gift_lat'];
                $longitude = (float) $data['gift_lng'];
            }

            // If no gift coordinates provided, fallback to regular address or coordinates
            if ($latitude === null || $longitude === null) {
                // Try address_id
                if (! empty($data['address_id'])) {
                    $address = $user->addresses()->find($data['address_id']);
                    if ($address) {
                        $latitude  = $address->latitude;
                        $longitude = $address->longitude;
                        $addressId = $address->id;
                    }
                }
                // Try direct coordinates (latitude/longitude or lat/lng)
                elseif (! empty($data['latitude']) && ! empty($data['longitude'])) {
                    $latitude  = (float) $data['latitude'];
                    $longitude = (float) $data['longitude'];
                } elseif (! empty($data['lat']) && ! empty($data['lng'])) {
                    $latitude  = (float) $data['lat'];
                    $longitude = (float) $data['lng'];
                }
            }

            // If still no coordinates, allow the order to proceed without a branch.
            // The admin can assign a branch/delivery later.
            if ($latitude === null || $longitude === null) {
                // Return early with null branch/address so caller can handle zero-fee flow
                return [
                    'latitude'   => null,
                    'longitude'  => null,
                    'branch'     => null,
                    'address_id' => null,
                ];
            }
        }
        // Approach 1: Address-based selection (regular orders)
        elseif (! empty($data['address_id'])) {
            $address = $user->addresses()->find($data['address_id']);
            if (! $address) {
                throw new \Exception(__('apis.invalid_address'));
            }

            $latitude  = $address->latitude;
            $longitude = $address->longitude;
            $addressId = $address->id;
        }
        // Approach 2: Direct coordinates selection (latitude/longitude or lat/lng)
        elseif (! empty($data['latitude']) && ! empty($data['longitude'])) {
            $latitude  = (float) $data['latitude'];
            $longitude = (float) $data['longitude'];
        } elseif (! empty($data['lat']) && ! empty($data['lng'])) {
            $latitude  = (float) $data['lat'];
            $longitude = (float) $data['lng'];
        } else {
            throw new \Exception(__('apis.location_required'));
        }

        // Find appropriate branch using polygon containment
        $branch = null;
        if ($latitude !== null && $longitude !== null) {
            $branch = $this->findBranchByLocation($latitude, $longitude);

            // If no branch found for the provided coordinates, throw error
            // Gift orders and regular orders are treated the same when coordinates are provided
            if (! $branch) {
                throw new \Exception(__('apis.address_not_in_branch_area'));
            }
        }

        return [
            'latitude'   => $latitude,
            'longitude'  => $longitude,
            'branch'     => $branch,
            'address_id' => $addressId,
        ];
    }

    /**
     * Find the appropriate branch based on location coordinates
     *
     * @param float $latitude
     * @param float $longitude
     * @return \App\Models\Branch|null
     */
    private function findBranchByLocation($latitude, $longitude)
    {
        $branches = \App\Models\Branch::where('status', 1)->get();

        foreach ($branches as $branch) {
            if ($this->isLocationInBranchPolygon($latitude, $longitude, $branch)) {
                return $branch;
            }
        }

        return null;
    }

    /**
     * Check if location coordinates are within branch delivery polygon
     *
     * @param float $latitude
     * @param float $longitude
     * @param \App\Models\Branch $branch
     * @return bool
     */
    private function isLocationInBranchPolygon($latitude, $longitude, $branch)
    {
        $polygon = json_decode($branch->polygon, true);

        if (! is_array($polygon) || empty($polygon)) {
            return false;
        }

        // Handle Leaflet polygon format (array of arrays)
        $points = $polygon[0] ?? $polygon;

        return $this->pointInPolygon([$latitude, $longitude], $points);
    }

    /**
     * Calculate delivery details for a specific branch and location
     *
     * @param float $latitude
     * @param float $longitude
     * @param \App\Models\Branch $branch
     * @return array
     */
    private function calculateDeliveryDetailsForBranch($latitude, $longitude, $branch, $deliveryType = 'immediate')
    {
        // Calculate distance to branch
        $distance = $this->haversineDistance(
            $latitude,
            $longitude,
            $branch->latitude,
            $branch->longitude
        );

        // Calculate delivery fee based on distance and site settings
        $fixedDeliveryFee          = (float) (
            $deliveryType === 'scheduled'
                ? getSiteSetting('scheduled_delivery_fee', 0)
                : getSiteSetting('ordinary_delivery_fee', getSiteSetting('delivery_fee', 0))
        );
        $perKmDeliveryFee          = (float) getSiteSetting('delivery_per_km_fee', 0);
        $deliveryDistanceThreshold = (float) getSiteSetting('delivery_distance_threshold', 0);

        $deliveryFee = 0;
        if ($distance <= $deliveryDistanceThreshold) {
            $deliveryFee = $fixedDeliveryFee;
        } else {
            $deliveryFee = $fixedDeliveryFee + ($distance * $perKmDeliveryFee);
        }

        return [
            'delivery_fee' => round($deliveryFee, 2),
            'branch_id'    => $branch->id,
            'distance'     => round($distance, 2),
        ];
    }

    /**
     * Validate cart and address for order creation
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return \App\Models\Cart
     * @throws \Exception
     */
    private function validateCartAndAddress($user, array $data)
    {
        $cart = $this->cartService->getCart($user);
        if (! $cart || $cart->items()->count() === 0) {
            throw new \Exception(__('cart.cart_is_empty'));
        }

        // Validate address for home delivery
        if (($data['delivery_type'] ?? null) === 'home_delivery') {
            $address = $user->addresses()->find($data['address_id'] ?? null);
            if (! $address) {
                throw new \Exception(__('apis.invalid_branch_or_address'));
            }
        }

        return $cart;
    }

    /**
     * Calculate delivery details including fee, branch, and distance
     * Enhanced to support both address_id and direct coordinates
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function calculateDeliveryDetails($user, array $data)
    {
        $deliveryFee = 0;
        $branchId    = null;
        $distance    = 0;
        $deliveryType = $data['delivery_type'] ?? 'immediate';

            $latitude  = null;
            $longitude = null;
            // If gift order, use gift coordinates or receiver address
            if (! empty($data['order_type']) && $data['order_type'] === 'gift') {
                // Try gift_latitude/gift_longitude first
                if (! empty($data['gift_latitude']) && ! empty($data['gift_longitude'])) {
                    $latitude  = (float) $data['gift_latitude'];
                    $longitude = (float) $data['gift_longitude'];
                }
                // Try gift_lat/gift_lng as alternative
                elseif (! empty($data['gift_lat']) && ! empty($data['gift_lng'])) {
                    $latitude  = (float) $data['gift_lat'];
                    $longitude = (float) $data['gift_lng'];
                }

                // If no gift coordinates, fallback to regular address or coordinates
                if ($latitude === null || $longitude === null) {
                    // Try address_id
                    if (! empty($data['address_id'])) {
                        $address = $user->addresses()->find($data['address_id']);
                        if ($address) {
                            $latitude  = $address->latitude;
                            $longitude = $address->longitude;
                        }
                    }
                    // Try latitude/longitude
                    elseif (! empty($data['latitude']) && ! empty($data['longitude'])) {
                        $latitude  = (float) $data['latitude'];
                        $longitude = (float) $data['longitude'];
                    }
                    // Try lat/lng
                    elseif (! empty($data['lat']) && ! empty($data['lng'])) {
                        $latitude  = (float) $data['lat'];
                        $longitude = (float) $data['lng'];
                    }
                }

                // If still no coordinates, allow order to proceed without branch and zero fees.
                if ($latitude === null || $longitude === null) {
                    return [
                        'delivery_fee' => 0,
                        'branch_id'    => null,
                        'distance'     => 0,
                    ];
                }
            }
            // Determine coordinates from address_id or direct lat/lng for ordinary orders
            elseif (! empty($data['address_id'])) {
                $address = $user->addresses()->find($data['address_id']);
                if (! $address) {
                    throw new \Exception(__('apis.invalid_branch_or_address'));
                }
                $latitude  = $address->latitude;
                $longitude = $address->longitude;
            } elseif (! empty($data['latitude']) && ! empty($data['longitude'])) {
                $latitude  = (float) $data['latitude'];
                $longitude = (float) $data['longitude'];
            } elseif (! empty($data['lat']) && ! empty($data['lng'])) {
                $latitude  = (float) $data['lat'];
                $longitude = (float) $data['lng'];
            } else {
                throw new \Exception(__('apis.location_required'));
            }

            // Find appropriate branch using coordinates
            $foundBranch = $this->findBranchByLocation($latitude, $longitude);

            if (! $foundBranch) {
                // If coordinates are provided but no branch found, throw error
                // Gift orders and regular orders are treated the same when coordinates are provided
                throw new \Exception(__('apis.address_not_in_branch_area'));
            }

            // Calculate delivery details for the found branch
            $deliveryDetails = $this->calculateDeliveryDetailsForBranch($latitude, $longitude, $foundBranch, $deliveryType);

            $branchId    = $foundBranch->id;
            $deliveryFee = $deliveryDetails['delivery_fee'];
            $distance    = $deliveryDetails['distance'];
       

        return [
            'delivery_fee' => $deliveryFee,
            'branch_id'    => $branchId,
            'distance'     => $distance,
        ];
    }

    /**
     * Public helper: Resolve branch info by address_id or direct coordinates, similar to checkout.
     * Accepts either data['address_id'] or data['latitude'] + data['longitude'] (also supports gift lat/lng).
     * Returns branch details and delivery calculations when available.
     *
     * @param \App\Models\User $user
     * @param array $data ['address_id'| 'latitude','longitude' | 'gift_latitude','gift_longitude', 'order_type']
     * @return array [
     *   'branch' => \App\Models\Branch|null,
     *   'branch_id' => int|null,
     *   'address_id' => int|null,
     *   'latitude' => float|null,
     *   'longitude' => float|null,
     *   'distance' => float, // km
     *   'delivery_fee' => float
     * ]
     */
    public function getBranchInfoByAddressOrCoordinates($user, array $data)
    {
        try {
            // Reuse the same logic used during checkout
            $locationData = $this->determineLocationAndBranch($user, $data);

            $branch = $locationData['branch'] ?? null;
            $lat    = $locationData['latitude'] ?? null;
            $lng    = $locationData['longitude'] ?? null;
            $deliveryType = $data['delivery_type'] ?? 'immediate';

            if ($branch && $lat !== null && $lng !== null) {
                $deliveryDetails = $this->calculateDeliveryDetailsForBranch($lat, $lng, $branch, $deliveryType);
                return [
                    'branch'       => $branch,
                    'branch_id'    => $branch->id,
                    'address_id'   => $locationData['address_id'] ?? null,
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'distance'     => $deliveryDetails['distance'] ?? 0,
                    'delivery_fee' => $deliveryDetails['delivery_fee'] ?? 0,
                ];
            }

            // No branch found (allowed in some gift flows) => return coordinates with zero fees
            return [
                'branch'       => null,
                'branch_id'    => null,
                'address_id'   => $locationData['address_id'] ?? null,
                'latitude'     => $lat,
                'longitude'    => $lng,
                'distance'     => 0,
                'delivery_fee' => 0,
            ];
        } catch (\Exception $e) {
            // For ordinary orders (non-gift), determineLocationAndBranch throws when outside any branch polygon
            // Expose a consistent shape with null branch
            return [
                'branch'       => null,
                'branch_id'    => null,
                'address_id'   => $data['address_id'] ?? null,
                'latitude'     => $data['latitude'] ?? $data['lat'] ?? $data['gift_latitude'] ?? $data['gift_lat'] ?? null,
                'longitude'    => $data['longitude'] ?? $data['lng'] ?? $data['gift_longitude'] ?? $data['gift_lng'] ?? null,
                'distance'     => 0,
                'delivery_fee' => 0,
                // Optionally, a message for callers that want to show errors
                // 'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate branch-level stock for all cart items before creating the order
     *
     * @param \App\Models\Cart $cart
     * @param int|null $branchId
     * @param string|null $orderType
     * @throws \Exception
     */
    private function validateBranchQuantities($cart, $branchId, $orderType = null)
    {
        // Skip branch stock validation for gift orders or when branch is not determined
        if (($orderType === 'gift') || empty($branchId)) {
            return;
        }

        $insufficient = [];
        foreach ($cart->items as $item) {
            if (! $item->product_id) {
                continue;
            }
            $branchProduct = BranchProduct::where('branch_id', $branchId)
                ->where('product_id', $item->product_id)
                ->first();
            $available = $branchProduct?->qty ?? 0;
            if ($available < $item->quantity) {
                $productName    = optional($item->product)->name ?? __('apis.product') . ' #' . $item->product_id;
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

        if (! empty($insufficient)) {
            // Throw a clear message listing problematic items with details
            $message = __('apis.insufficient_stock_in_branch') . ': ' . implode(' | ', $insufficient);
            throw new \Exception($message);
        }
    }

    /**
     * Create order from cart data and delivery details
     *
     * @param \App\Models\User $user
     * @param \App\Models\Cart $cart
     * @param array $data
     * @param array $deliveryDetails
     * @return \App\Models\Order
     */
    private function createOrderFromCartData($user, $cart, array $data, array $deliveryDetails)
    {

        $address = null;
        // Only create a new address when an address_id isn't provided and this is NOT a gift order
        if (empty($data['address_id']) && (($data['order_type'] ?? null) !== 'gift')) {
            $address = $this->addressService->store($data);
        }

        // Prepare order data
        $giftFee = (($data['order_type'] ?? null) === 'gift') ? (float) getSiteSetting('gift_fee', 0) : 0;

        $orderData = [
            'order_number'      => $this->generateOrderNumber(),
            'user_id'           => $user->id,
            'status'            => 'pending',
            'delivery_type'     => $data['delivery_type'] ?? null,
            'branch_id'         => $deliveryDetails['branch_id'],
            'address_id'        => $data['address_id'] ?? $address->id ?? null,
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'subtotal'          => $cart->subtotal,
            'discount_amount'   => $cart->discount ?? 0,
            'discount_code'     => $cart->coupon_code ?? null,
            'coupon_id'         => null,
            'coupon_amount'     => $cart->coupon_value ?? 0,
            'delivery_fee'      => $deliveryDetails['delivery_fee'],
            'gift_fee'          => $giftFee,
            'wallet_deduction'  => $cart->wallet_deduction ?? 0,
            'total'             => $cart->total + $deliveryDetails['delivery_fee'] + $giftFee,

            'order_type'        => $data['order_type'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'vat_amount'        => $cart->vat_amount ?? 0,

            // Gift/recipient fields (nullable)
            'reciver_name'      => $data['reciver_name'] ?? null,
            'reciver_phone'     => $data['reciver_phone'] ?? null,
            'gift_address_name' => $data['gift_address_name'] ?? null,
            'gift_city_id'      => $data['gift_city_id'] ?? null,
            'gift_districts_id' => $data['gift_districts_id'] ?? null,
            'gift_latitude'     => isset($data['gift_latitude']) ? (float) $data['gift_latitude'] : null,
            'gift_longitude'    => isset($data['gift_longitude']) ? (float) $data['gift_longitude'] : null,
            'message'           => $data['message'] ?? null,
            'whatsapp'          => isset($data['whatsapp']) ? (int) $data['whatsapp'] : 0,
            'hide_sender'       => isset($data['hide_sender']) ? (int) $data['hide_sender'] : 0,
            'schedule_date'     => $data['schedule_date'] ?? null,
            'schedule_time'     => $data['schedule_time'] ?? null,
        ];

        $order = Order::create($orderData);

        // Attach coupon if present
        $this->attachCouponToOrder($order, $cart);

        // Move cart items to order items
        $this->transferCartItemsToOrder($order, $cart);

        // Deduct product quantities from inventory
        $this->deductProductQuantities($order);

        // Clear the cart immediately for ALL payment methods
        $this->cartService->clearCart($user);

        return $order;
    }

    /**
     * Attach coupon to order if cart has a coupon code
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Cart $cart
     * @return void
     */
    private function attachCouponToOrder($order, $cart)
    {
        if (! empty($cart->coupon_code)) {
            $coupon = \App\Models\Coupon::where('coupon_num', $cart->coupon_code)->first();
            if ($coupon) {
                // Only attempt to write coupon_id if the DB actually has the column
                if (Schema::hasColumn('orders', 'coupon_id')) {
                    try {
                        $order->update(['coupon_id' => $coupon->id]);
                    } catch (\Exception $e) {
                        Log::warning('Failed to attach coupon_id to order', [
                            'order_id'  => $order->id,
                            'coupon_id' => $coupon->id,
                            'error'     => $e->getMessage(),
                        ]);
                    }
                } else {
                    // The orders table doesn't have coupon_id yet; discount_code was already saved.
                    Log::info('Orders table missing coupon_id column; skipped updating coupon_id', ['order_id' => $order->id]);
                }
            }
        }
    }

    /**
     * Transfer cart items to order items
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Cart $cart
     * @return void
     */
    private function transferCartItemsToOrder($order, $cart)
    {
        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id'          => $cartItem->product_id,
                'quantity'            => $cartItem->quantity,
                'weight_option_id'    => $cartItem->weight_option_id,
                'cutting_option_id'   => $cartItem->cutting_option_id,
                'packaging_option_id' => $cartItem->packaging_option_id,
                'price'               => $cartItem->price,
                'discount_amount'     => $cartItem->discount_amount,
                'total'               => $cartItem->total,
            ]);
        }
    }
    /**
     * Process payment for the order
     *
     * @param \App\Models\Order $order
     * @param \App\Models\User $user
     * @param array $data
     * @param array $deliveryDetails
     * @return array
     * @throws \Exception
     */
    private function processOrderPayment($order, $user, array $data, array $deliveryDetails)
    {
        // Check if this is a wallet payment (payment_method_id = 5)
        if (in_array($data['payment_method_id'], [5])) {
            // Handle wallet payment - immediate success

            // Clear user's cart after successful wallet payment
            $this->clearUserCart($user);

            // Handle coupon usage if coupon was applied
            if ($order->coupon_id) {
                $this->handleCouponUsage($order);
            }

            // Send notification to admins
            $orderNum = $order->order_number ?? $order->id;
            $this->sendNewOrderNotificationToAdmins($orderNum, $order->id);
            if ($order->wallet_deduction > 0) {
                $this->transactionService->createWalletPaymentTransaction($order->user_id, $order->wallet_deduction, $order);
            }
            DB::commit();
            return [
                'status'               => 'success',
                'message'              => __('apis.order_created'),
                'order'                => $order->load('items'),
                'delivery_distance_km' => $deliveryDetails['distance'],
                'delivery_fee'         => $deliveryDetails['delivery_fee'],
            ];
        }

        // Handle electronic payments (MyFatoorah)
        $paymentMethod = PaymentMethod::tryFrom($data['payment_method_id']);
        if ($paymentMethod === null) {
            throw new \Exception('Invalid payment method');
        }

        $gateway       = $this->paymentService->getPaymentGateway($paymentMethod, $data);
        $paymentResult = $this->paymentService->initializeMyFatoorahPayment($order, $user, [
            'gateway'        => $gateway,
            'payment_method' => $paymentMethod->label(),
        ]);

        if (isset($paymentResult['status']) && $paymentResult['status'] === 'error') {
            DB::rollBack();
            return $paymentResult;
        }

        // Update order with payment URL
        if (isset($paymentResult['invoiceURL'])) {
            $order->update(['payment_url' => $paymentResult['invoiceURL']]);
        }

        // Send notification to admins for electronic payments
        $orderNum = $order->order_number ?? $order->id;
        $this->sendNewOrderNotificationToAdmins($orderNum, $order->id);

        // For electronic payments (payment_method != 5), refund wallet deduction back to user
        // Wallet was already deducted in CartService, but payment will be confirmed via webhook
        // So we refund it now and will deduct again when webhook confirms payment
        if ($order->wallet_deduction > 0) {
            $user->increment('wallet_balance', $order->wallet_deduction);

            Log::info('Wallet deduction refunded for electronic payment (will be deducted on webhook confirmation)', [
                'order_id'         => $order->id,
                'user_id'          => $order->user_id,
                'wallet_deduction' => $order->wallet_deduction,
                'payment_method'   => $order->payment_method,
            ]);
        }

        DB::commit();
        return [
            'status'               => 'success',
            'message'              => __('apis.order_created'),
            'payment_url'          => $paymentResult['invoiceURL'],
            'order'                => $order->load('items'),
            'delivery_distance_km' => $deliveryDetails['distance'],
            'delivery_fee'         => $deliveryDetails['delivery_fee'],
        ];
    }

    protected function sendNewOrderNotificationToAdmins($orderNum, $id): void
    {
        $message = 'يوجد طلب حجز جديد برقم #' . $orderNum;
        $admins  = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title'    => [
                    'ar' => 'طلب جديد',
                    'en' => 'New Order',
                ],
                'body'     => [
                    'ar' => $message,
                    'en' => $message,
                ],
                'type'     => 'new_order',
                'order_id' => $id,
            ]));
        }
    }

    protected function generateOrderNumber()
    {
        return '#' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function handlePaymentCallback(Request $request)
    {
        return $this->paymentService->handlePaymentCallback($request);
    }

    /**
     * Check if address is within branch polygon and calculate distance to branch location
     *
     * @param float $addressLat
     * @param float $addressLng
     * @param \App\Models\Branch $branch
     * @return array ['in_polygon' => bool, 'distance_km' => float]
     */
    public function checkAddressWithinBranchAreaAndDistance($addressLat, $addressLng, $branch)
    {
        // 1. Check if address is within polygon
        $polygon   = json_decode($branch->polygon, true);
        $inPolygon = false;
        if (is_array($polygon) && count($polygon) > 0) {
            // Leaflet polygons are arrays of arrays (could be multi-polygon)
            $points    = $polygon[0];
            $inPolygon = $this->pointInPolygon([$addressLat, $addressLng], $points);
        }

        // 2. Calculate distance to branch location
        $distance = $this->haversineDistance($addressLat, $addressLng, $branch->latitude, $branch->longitude);

        return [
            'in_polygon'  => $inPolygon,
            'distance_km' => $distance,
        ];
    }

    /**
     * Point in Polygon (Ray Casting algorithm)
     * @param array $point [lat, lng]
     * @param array $polygon [[lat, lng], ...]
     * @return bool
     */
    private function pointInPolygon($point, $polygon)
    {
        // Defensive: ensure point has both lat and lng
        if (! is_array($point) || ! array_key_exists(0, $point) || ! array_key_exists(1, $point)) {
            return false;
        }
        $x      = (float) $point[1]; // lng
        $y      = (float) $point[0]; // lat
        $inside = false;
        $n      = count($polygon);
        // Remove duplicate last point if polygon is closed
        if ($n > 1 && isset($polygon[0][0], $polygon[0][1], $polygon[$n - 1][0], $polygon[$n - 1][1]) && $polygon[0][0] === $polygon[$n - 1][0] && $polygon[0][1] === $polygon[$n - 1][1]) {
            $n = $n - 1;
        }
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            // Root-level validation: skip if either point is not a valid [lat, lng] array
            if (! is_array($polygon[$i]) || count($polygon[$i]) < 2 || ! is_array($polygon[$j]) || count($polygon[$j]) < 2) {
                continue;
            }

            $xi = $polygon[$i]['lng'];
            $yi = $polygon[$i]['lat'];
            $xj = $polygon[$j]['lng'];
            $yj = $polygon[$j]['lat'];
            // Ray-casting: check if the edge crosses the horizontal ray to the right of the point
            $onEdge = (($y == $yi && $y == $yj) && ($x >= min($xi, $xj) && $x <= max($xi, $xj))) ||
                (($x == $xi && $x == $xj) && ($y >= min($yi, $yj) && $y <= max($yi, $yj)));
            if ($onEdge) {
                return true; // Point is exactly on a polygon edge
            }
            if ((($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi)) {
                $inside = ! $inside;
            }
        }
        return $inside;
    }

    /**
     * Haversine distance in kilometers
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $lat1        = deg2rad($lat1);
        $lng1        = deg2rad($lng1);
        $lat2        = deg2rad($lat2);
        $lng2        = deg2rad($lng2);
        $dlat        = $lat2 - $lat1;
        $dlng        = $lng2 - $lng1;
        $a           = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c           = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Calculate distance between address and branch and determine delivery fee
     *
     * @param \App\Models\Address $address
     * @param \App\Models\Branch $branch
     * @return array ['distance_km' => float, 'delivery_fee' => float, 'in_polygon' => bool]
     */
    public function calculateDistanceAndDeliveryFee($address, $branch, $deliveryType = 'immediate')
    {
        // Calculate distance using haversine formula
        $distance = $this->haversineDistance(
            $address->latitude,
            $address->longitude,
            $branch->latitude,
            $branch->longitude
        );

        // Check if address is within branch polygon
        $polygon   = json_decode($branch->polygon, true);
        $inPolygon = false;
        if (is_array($polygon) && count($polygon) > 0) {
            $points    = $polygon[0];
            $inPolygon = $this->pointInPolygon([$address->latitude, $address->longitude], $points);
        }

        // Calculate delivery fee based on distance and site settings
        $deliveryFee = 0;

        $fixedDeliveryFee          = (float) (
            $deliveryType === 'scheduled'
                ? getSiteSetting('scheduled_delivery_fee', 0)
                : getSiteSetting('ordinary_delivery_fee', getSiteSetting('delivery_fee', 0))
        );
        $perKmDeliveryFee          = (float) getSiteSetting('delivery_per_km_fee', 0);
        $deliveryDistanceThreshold = (float) getSiteSetting('delivery_distance_threshold', 0);

        // Apply delivery fee calculation logic
        if ($distance <= $deliveryDistanceThreshold) {
            // Fixed delivery fee within threshold
            $deliveryFee = $fixedDeliveryFee;
        } else {
            // Per kilometer delivery fee when distance exceeds threshold
            $deliveryFee = $fixedDeliveryFee + ($distance * $perKmDeliveryFee);
        }

        return [
            'distance_km'  => round($distance, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'in_polygon'   => $inPolygon,
        ];
    }

    /**
     * Deduct product quantities when order is created
     */
    private function deductProductQuantities(Order $order)
    {
        foreach ($order->items as $orderItem) {
            if (! $orderItem->product_id) {
                continue;
            }
            // Prefer branch-level stock deduction when branch_id is present
            if (! empty($order->branch_id)) {
                $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $orderItem->product_id)
                    ->first();

                if ($branchProduct) {
                    if ($branchProduct->qty >= $orderItem->quantity) {
                        $branchProduct->decrement('qty', $orderItem->quantity);
                    } else {
                        // Return detailed error message with product name and quantities
                        $productName = optional($orderItem->product)->name ?? __('apis.product') . ' #' . $orderItem->product_id;
                        $message     = sprintf(
                            '%s: %s ',
                            __('apis.insufficient_stock_in_branch'),
                            $productName,

                        );
                        throw new \Exception($message);
                    }
                } else {
                    // Return detailed error message when product not found in branch
                    $productName = optional($orderItem->product)->name ?? __('apis.product') . ' #' . $orderItem->product_id;
                    $message     = sprintf(
                        '%s: %s ',
                        __('apis.insufficient_stock_in_branch'),
                        $productName,

                    );
                    throw new \Exception($message);
                }
            } else {
                // Fallback: global product stock deduction
                $product = \App\Models\Product::find($orderItem->product_id);
                if ($product && $product->quantity >= $orderItem->quantity) {
                    $product->decrement('quantity', $orderItem->quantity);
                } else {
                    // Return detailed error message for global stock
                    $productName = optional($product)->name ?? __('apis.product') . ' #' . $orderItem->product_id;
                    $available   = $product ? $product->quantity : 0;
                    $message     = sprintf(
                        '%s: %s',
                        __('apis.insufficient_stock'),
                        $productName,

                    );
                    throw new \Exception($message);
                }
            }
        }
    }

    /**
     * Restore product quantities when order is cancelled
     */
    private function restoreProductQuantities(Order $order)
    {
        foreach ($order->items as $orderItem) {
            if (! $orderItem->product_id) {
                continue;
            }
            if (! empty($order->branch_id)) {
                $branchProduct = BranchProduct::where('branch_id', $order->branch_id)
                    ->where('product_id', $orderItem->product_id)
                    ->first();
                if ($branchProduct) {
                    $branchProduct->increment('qty', $orderItem->quantity);
                }
            } else {
                $product = \App\Models\Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('quantity', $orderItem->quantity);
                }
            }
        }
    }

    /**
     * Cancel an order and restore product quantities
     */
    public function cancelOrder(Order $order, $reason = null, $cancelledBy = null)
    {
        try {
            DB::beginTransaction();

            // Update order status to cancelled
            $order->update([
                'status'        => 'cancelled',
                'cancel_reason' => $reason,
            ]);

            // Restore product quantities
            $this->restoreProductQuantities($order);

            // Log the cancellation
            Log::info('Order cancelled and quantities restored', [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'cancelled_by' => $cancelledBy ? get_class($cancelledBy) . ':' . $cancelledBy->id : 'system',
                'reason'       => $reason,
            ]);

            DB::commit();
            return $order->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Confirm payment and finalize order
     */
    public function confirmPayment(Order $order, $paymentReference = null)
    {
        try {
            DB::beginTransaction();

            // Restore order if soft deleted
            if ($order->trashed()) {
                $order->restore();
            }

            // Update order payment status
            $order->update([
                'payment_status'    => 'success',
                'payment_reference' => $paymentReference,
            ]);

            // Apply wallet deduction for non-wallet payment methods
            // For payment_method != 5, wallet was refunded during order creation
            // Now we deduct it again after payment confirmation
            if ($order->wallet_deduction > 0 && $order->payment_method != 5) {
                $order->user->decrement('wallet_balance', (float) $order->wallet_deduction);

                // Create transaction record
                $this->transactionService->createWalletPaymentTransaction($order->user_id, $order->wallet_deduction, $order);

                Log::info('Wallet deduction applied after payment confirmation', [
                    'order_id'         => $order->id,
                    'user_id'          => $order->user_id,
                    'wallet_deduction' => $order->wallet_deduction,
                    'payment_method'   => $order->payment_method,
                ]);
            }

            // Clear user's cart after successful payment
            $this->clearUserCart($order->user);

            // Handle coupon usage if coupon was applied
            if ($order->coupon_id) {
                $this->handleCouponUsage($order);
            }

            // Log the payment confirmation
            Log::info('Order payment confirmed', [
                'order_id'          => $order->id,
                'order_number'      => $order->order_number,
                'payment_reference' => $paymentReference,
                'cart_cleared'      => true,
                'coupon_processed'  => $order->coupon_id ? true : false,
                'wallet_deducted'   => $order->wallet_deduction > 0 && $order->payment_method != 5,
            ]);

            DB::commit();
            return $order->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to confirm payment', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Clear user's cart after successful order completion
     */
    private function clearUserCart(User $user)
    {
        try {
            $cart = $this->cartService->getCart($user);
            if ($cart) {
                // Clear all cart items
                $cart->items()->delete();

                // Clear cart-level discounts and coupons
                $cart->update([
                    'coupon_id'        => null,
                    'discount_amount'  => 0,
                    'coupon_amount'    => 0,
                    'discount_code'    => null,
                    'wallet_deduction' => 0,
                ]);

                Log::info('Cart cleared after successful order', [
                    'user_id' => $user->id,
                    'cart_id' => $cart->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear cart after order completion', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            // Don't throw exception as this is cleanup - order is already successful
        }
    }

    /**
     * Handle coupon usage after successful payment
     */
    private function handleCouponUsage(Order $order)
    {
        try {
            if ($order->coupon_id) {
                $coupon = \App\Models\Coupon::find($order->coupon_id);
                if ($coupon) {
                    // Increment usage count
                    $coupon->increment('used_times');

                    Log::info('Coupon usage recorded', [
                        'coupon_id'       => $coupon->id,
                        'order_id'        => $order->id,
                        'user_id'         => $order->user_id,
                        'discount_amount' => $order->coupon_amount,
                        'used_times'      => $coupon->used_times,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle coupon usage', [
                'order_id'  => $order->id,
                'coupon_id' => $order->coupon_id,
                'error'     => $e->getMessage(),
            ]);
            // Don't throw exception as this is cleanup - order is already successful
        }
    }

    /**
     * Handle payment failure and restore quantities
     */
    public function handlePaymentFailure(Order $order, $failureReason = null)
    {
        try {
            DB::beginTransaction();

            // Restore order if soft deleted
            if ($order->trashed()) {
                $order->restore();
            }

            // Update order status to failed
            $order->update([
                'payment_status' => 'failed',
                'status'         => 'cancelled',
                'cancel_reason'  => $failureReason,
            ]);

            // Restore product quantities since payment failed
            $this->restoreProductQuantities($order);

            // Log the payment failure
            Log::info('Order payment failed and quantities restored', [
                'order_id'       => $order->id,
                'order_number'   => $order->order_number,
                'failure_reason' => $failureReason,
            ]);

            DB::commit();
            return $order->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to handle payment failure', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Request a refund for an order
     */
    public function requestRefund($orderId, $reason)
    {
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::findOrFail($orderId);

            // Check if user owns this order
            if ($order->user_id !== auth()->id()) {
                throw new \Exception(__('admin.unauthorized_action'));
            }

            // Check if order can be refunded (use refund_status lifecycle)
            if ($order->refund_status === 'request_refund') {
                throw new \Exception(__('admin.refund_already_requested'));
            }

            // Main order cannot be refunded if cancelled; for completed refunds, check refund_status
            if ($order->status === 'cancelled' || in_array($order->refund_status, ['refunded', 'request_rejected'])) {
                throw new \Exception(__('admin.order_cannot_be_refunded'));
            }

            // Check if refund request already exists
            if ($order->refundOrder) {
                throw new \Exception(__('admin.refund_already_exists'));
            }

            // Create refund request
            $refundOrder = \App\Models\RefundOrder::create([
                'order_id'      => $order->id,
                'delivery_id'   => 1, // Default delivery person, will be updated by admin
                'refund_number' => \App\Models\RefundOrder::generateRefundNumber(),
                'amount'        => null, // Will be set by admin when accepting
                'status'        => \App\Models\RefundOrder::STATUS_PENDING,
                'reason'        => $reason,
            ]);

            // Update refund status without changing main status
            $order->update(['refund_status' => 'request_refund']);

            return $refundOrder;
        });
    }

    public function getMyOrders(?string $status, ?string $search)
    {
        $user = auth()->user();

        return Order::with(['user', 'items.product'])
            ->where('user_id', $user->id)
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            }, function ($q) {
                $q->whereIn('status', ['new', 'delivered', 'confirmed']);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', fn($q2) => $q2->where('name', 'LIKE', "%{$search}%"));
                });
            })
            ->latest()
            ->get();
    }

    public function getMyOrderById(int $id)
    {
        $user = auth()->user();

        if ($user->type !== 'delivery') {
            return null;
        }

        return Order::with(['user', 'items.product'])
            ->whereIn('status', ['new', 'delivered', 'confirmed'])
            ->where('user_id', $user->id)
            ->find($id);
    }

    public function updateMyOrderStatus(int $id, string $status)
    {
        $user = auth()->user();

        if ($user->type !== 'delivery') {
            return null;
        }

        $order = Order::whereIn('status', ['new', 'delivered', 'confirmed'])
            ->where('user_id', $user->id)
            ->find($id);

        if (! $order) {
            return null;
        }

        // Delivery can only update to confirmed or delivered
        if (! in_array($status, ['delivered', 'confirmed'])) {
            return false;
        }

        // Validate status transition
        if (! $this->isValidStatusTransition($order->status, $status)) {
            throw new \Exception(__('admin.invalid_status_transition'));
        }

        $order->status = $status;
        $order->save();

        return $order;
    }

/**
 * Validate if a status transition is allowed
 *
 * @param string $currentStatus
 * @param string $newStatus
 * @return bool
 */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        // Define allowed transitions
        $allowedTransitions = [
            'pending'        => ['new', 'cancelled'],
            'new'            => ['confirmed', 'cancelled', 'problem'],
            'confirmed'      => ['delivered', 'cancelled', 'problem'],
            'delivered'      => ['request_refund'],
            'problem'        => ['cancelled', 'new'],
            'cancelled'      => ['refunded'],
            'request_refund' => ['refunded', 'cancelled'],
            'refunded'       => [],
        ];

        // Check if current status exists in allowed transitions
        if (! isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        // Check if new status is in the allowed list for current status
        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

}
