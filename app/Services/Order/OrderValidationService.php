<?php

namespace App\Services\Order;

use App\Models\Cart;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\CartRepository;

/**
 * OrderValidationService
 * 
 * Handles all validation logic for order creation
 * Validates cart, address, stock, payment methods, etc.
 */
class OrderValidationService
{
    protected $cartRepository;
    protected $addressRepository;
    protected $inventoryService;

    public function __construct(
        CartRepository $cartRepository,
        AddressRepository $addressRepository,
        InventoryService $inventoryService
    ) {
        $this->cartRepository = $cartRepository;
        $this->addressRepository = $addressRepository;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Validate cart and address before order creation
     *
     * @param User $user
     * @param array $data
     * @return array ['cart' => Cart, 'address' => Address|null]
     * @throws \Exception
     */
    public function validateCartAndAddress(User $user, array $data): array
    {
        // Get user cart
        $cart = $this->cartRepository->getCart($user);

        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception(__('apis.cart_is_empty'));
        }

        // Validate cart items
        $this->validateCartItems($cart);

        // Validate address for non-gift orders
        $address = null;
        $orderType = $data['order_type'] ?? null;

        if ($orderType !== 'gift') {
            $address = $this->validateAddress($user, $data);
        }

        return [
            'cart' => $cart,
            'address' => $address,
        ];
    }

    /**
     * Validate cart items (products exist, are active, etc.)
     *
     * @param Cart $cart
     * @throws \Exception
     */
    private function validateCartItems(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            if (!$item->product) {
                throw new \Exception(__('apis.invalid_product_in_cart'));
            }

            if (!$item->product->is_active) {
                throw new \Exception(__('apis.inactive_product_in_cart', ['product' => $item->product->name]));
            }

            if ($item->quantity <= 0) {
                throw new \Exception(__('apis.invalid_quantity_in_cart'));
            }
        }
    }

    /**
     * Validate address for order
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\Address|null
     * @throws \Exception
     */
    private function validateAddress(User $user, array $data)
    {
        // If address_id is provided, validate it
        if (!empty($data['address_id'])) {
            $address = $this->addressRepository->getUserAddress($user, $data['address_id']);

            if (!$address) {
                throw new \Exception(__('apis.invalid_address'));
            }

            return $address;
        }

        // If coordinates are provided, address is optional (will be created later)
        if (!empty($data['latitude']) && !empty($data['longitude'])) {
            return null;
        }

        if (!empty($data['lat']) && !empty($data['lng'])) {
            return null;
        }

        // For delivery orders, address or coordinates are required
        if (($data['delivery_type'] ?? null) === 'delivery') {
            throw new \Exception(__('apis.address_required'));
        }

        return null;
    }

    /**
     * Validate payment method
     *
     * @param array $data
     * @throws \Exception
     */
    public function validatePaymentMethod(array $data): void
    {
        if (empty($data['payment_method_id'])) {
            throw new \Exception(__('apis.payment_method_required'));
        }

        $validPaymentMethods = [1, 2, 3 ,4 ,5]; // 1=cash, 2=card, 3=wallet, etc.
        
        if (!in_array($data['payment_method_id'], $validPaymentMethods)) {
            throw new \Exception(__('apis.invalid_payment_method'));
        }
    }

    /**
     * Validate wallet balance for wallet payment
     *
     * @param User $user
     * @param float $totalAmount
     * @throws \Exception
     */
    public function validateWalletBalance(User $user, float $totalAmount): void
    {
        if ($user->wallet_balance < $totalAmount) {
            throw new \Exception(__('apis.insufficient_wallet_balance'));
        }
    }

    /**
     * Validate delivery type
     *
     * @param array $data
     * @throws \Exception
     */
    public function validateDeliveryType(array $data): void
    {
        $deliveryType = $data['delivery_type'] ?? null;

        if (!$deliveryType) {
            throw new \Exception(__('apis.delivery_type_required'));
        }

        $validDeliveryTypes = ['scheduled', 'immediate'];

        if (!in_array($deliveryType, $validDeliveryTypes)) {
            throw new \Exception(__('apis.invalid_delivery_type'));
        }

    
    }

    /**
     * Validate order type
     *
     * @param array $data
     * @throws \Exception
     */
    public function validateOrderType(array $data): void
    {
        $orderType = $data['order_type'] ?? 'ordinary';

        $validOrderTypes = ['ordinary', 'gift'];

        if (!in_array($orderType, $validOrderTypes)) {
            throw new \Exception(__('apis.invalid_order_type'));
        }

        // If gift order, validate gift-specific fields
        if ($orderType === 'gift') {
            $this->validateGiftOrderFields($data);
        }
    }

    /**
     * Validate gift order specific fields
     *
     * @param array $data
     * @throws \Exception
     */
    private function validateGiftOrderFields(array $data): void
    {
        if (empty($data['reciver_name'])) {
            throw new \Exception(__('apis.receiver_name_required'));
        }

        if (empty($data['reciver_phone'])) {
            throw new \Exception(__('apis.receiver_phone_required'));
        }

        // Gift coordinates or address are optional but recommended
        // No strict validation here
    }

    /**
     * Validate minimum order amount
     *
     * @param float $subtotal
     * @throws \Exception
     */
    public function validateMinimumOrderAmount(float $subtotal): void
    {
        $minimumOrder = (float) getSiteSetting('minimum_order_amount', 0);

        if ($minimumOrder > 0 && $subtotal < $minimumOrder) {
            throw new \Exception(__('apis.minimum_order_amount_not_met', ['amount' => $minimumOrder]));
        }
    }

    /**
     * Validate coupon if applied
     *
     * @param Cart $cart
     * @throws \Exception
     */
    public function validateCoupon(Cart $cart): void
    {
        if ($cart->coupon_id) {
            $coupon = $cart->coupon;

            if (!$coupon) {
                throw new \Exception(__('apis.invalid_coupon'));
            }

            // Additional coupon validation can be added here
            // (expiry, usage limits, etc.)
        }
    }
}

