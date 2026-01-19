<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Services\TransactionService;
use App\Services\PaymentService;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\Log;

/**
 * OrderPaymentService
 *
 * Handles all payment processing logic for orders
 * Manages payment gateway integration, wallet payments, and payment status updates
 */
class OrderPaymentService
{
    protected $orderRepository;
    protected $transactionService;
    protected $paymentService;

    public function __construct(
        OrderRepository $orderRepository,
        TransactionService $transactionService,
        PaymentService $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->transactionService = $transactionService;
        $this->paymentService = $paymentService;
    }

    /**
     * Process order payment based on payment method
     *
     * @param Order $order
     * @param array $data
     * @param array $options - Optional parameters like callback_url and error_url for API
     * @return array ['payment_url' => string|null, 'payment_status' => string]
     * @throws \Exception
     */
    public function processPayment(Order $order, array $data, array $options = []): array
    {
        $paymentMethodId = (int) ($data['payment_method_id'] ?? 0);

        // Payment Method: Offline (e.g., Cash/Bank Transfer). Skip MyFatoorah.
        if ($paymentMethodId == 5) {
            return $this->processCashPayment($order);
        }

        // Payment Method: Electronic Payment (MyFatoorah)
        if ($paymentMethodId != 5) {
            return $this->processElectronicPayment($order, $data, $options);
        }

        throw new \Exception(__('apis.invalid_payment_method'));
    }

    /**
     * Process cash on delivery payment
     *
     * @param Order $order
     * @return array
     */
    private function processCashPayment(Order $order): array
    {
        $this->orderRepository->update($order, [
            'payment_status' => 'pending',
        ]);

        // For cash payment, if wallet deduction was applied, create transaction record
        if ($order->wallet_deduction > 0) {
            $this->transactionService->createWalletPaymentTransaction(
                $order->user_id,
                $order->wallet_deduction,
                $order
            );

            Log::info('Wallet deduction transaction created for cash payment', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'wallet_deduction' => $order->wallet_deduction,
            ]);
        }

        return [
            'payment_url' => null,
            'payment_status' => 'pending',
        ];
    }

    /**
     * Process electronic payment via MyFatoorah
     *
     * @param Order $order
     * @param array $data
     * @param array $options - Optional parameters like callback_url and error_url for API
     * @return array
     * @throws \Exception
     */
    private function processElectronicPayment(Order $order, array $data, array $options = []): array
    {
        try {
            $paymentMethod = PaymentMethod::tryFrom($data['payment_method_id'] ?? 0);
            
            // Default to VISA if not found, or handle error
            if (!$paymentMethod) {
                 // Try to guess or default
                 $paymentMethod = PaymentMethod::VISA;
            }

            $gateway = $this->paymentService->getPaymentGateway($paymentMethod, $data);
            
            $paymentResult = $this->paymentService->initializeMyFatoorahPayment($order, $order->user, array_merge($options, [
                'gateway' => $gateway,
            ]));

            if (isset($paymentResult['status']) && $paymentResult['status'] === 'error') {
                 throw new \Exception($paymentResult['message']);
            }

            $paymentUrl = $paymentResult['invoiceURL'];

            $this->orderRepository->update($order, [
                'payment_status' => 'pending',
                'payment_url' => $paymentUrl
            ]);

            // IMPORTANT: For electronic payments, wallet was already deducted in CartService
            // But payment will be confirmed via webhook, so we refund it now
            // and will deduct again when webhook confirms payment
            if ($order->wallet_deduction > 0) {
                $user = $order->user;
                $this->orderRepository->incrementUserWallet($user, $order->wallet_deduction);

                Log::info('Wallet deduction refunded for electronic payment (will be deducted on webhook confirmation)', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'wallet_deduction' => $order->wallet_deduction,
                    'payment_method' => $order->payment_method_id,
                ]);
            }

            return [
                'payment_url' => $paymentUrl,
                'payment_status' => 'pending',
            ];
        } catch (\Exception $e) {
            Log::error('MyFatoorah payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception(__('apis.payment_gateway_error'));
        }
    }


    /**
     * Process wallet payment
     *
     * @param Order $order
     * @return array
     * @throws \Exception
     */
    private function processWalletPayment(Order $order): array
    {
        $user = $order->user;

        // Validate wallet balance
        if ($user->wallet_balance < $order->total) {
            throw new \Exception(__('apis.insufficient_wallet_balance'));
        }

        // Deduct from wallet
        $this->orderRepository->decrementUserWallet($user, $order->total);

        // Update order payment status
        $this->orderRepository->update($order, [
            'payment_status' => 'paid',
            'status' => 'new', // Move to 'new' status after successful payment
        ]);

        // Create wallet payment transaction if wallet deduction was applied
        if ($order->wallet_deduction > 0) {
            $this->transactionService->createWalletPaymentTransaction(
                $order->user_id,
                $order->wallet_deduction,
                $order
            );
        }

        Log::info('Wallet payment processed', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'amount' => $order->total,
            'wallet_deduction' => $order->wallet_deduction,
        ]);

        return [
            'payment_url' => null,
            'payment_status' => 'paid',
        ];
    }

    /**
     * Confirm payment after successful gateway callback
     *
     * @param Order $order
     * @return bool
     */
    public function confirmPayment(Order $order): bool
    {
        $updated = $this->orderRepository->update($order, [
            'payment_status' => 'success',
            'status' => 'new', // Move to 'new' status after payment confirmation
        ]);

        if ($updated) {
            Log::info('Payment confirmed for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        }

        return $updated;
    }

    /**
     * Refund payment to user wallet
     *
     * @param Order $order
     * @return bool
     */
    public function refundToWallet(Order $order): bool
    {
        $user = $order->user;

        if (!$user) {
            return false;
        }

        // Determine refund amount: use refund_amount if set, otherwise use total
        $refundAmount = $order->refund_amount ?? $order->total;

        // Add refund amount to wallet
        $this->orderRepository->incrementUserWallet($user, $refundAmount);
        $this->transactionService->createWalletRefundTransaction($order->user_id, $refundAmount, $order->order_number);

        Log::info('Payment refunded to wallet', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'amount' => $refundAmount,
            'is_refund_order' => $order->is_refund ?? false,
        ]);

        return true;
    }

    /**
     * Handle payment failure
     *
     * @param Order $order
     * @param string|null $reason
     * @return bool
     */
    public function handlePaymentFailure(Order $order, ?string $reason = null): bool
    {
        $updated = $this->orderRepository->update($order, [
            'payment_status' => 'failed',
            'status' => 'cancelled',
        ]);

        if ($updated) {
            Log::warning('Payment failed for order', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $reason,
            ]);
        }

        return $updated;
    }

    /**
     * Check if order payment is completed
     *
     * @param Order $order
     * @return bool
     */
    public function isPaymentCompleted(Order $order): bool
    {
        return $order->payment_status === 'paid';
    }

    /**
     * Check if order payment is pending
     *
     * @param Order $order
     * @return bool
     */
    public function isPaymentPending(Order $order): bool
    {
        return $order->payment_status === 'pending';
    }

    /**
     * Clean phone number for MyFatoorah
     * Removes non-numeric characters, leading zeros, and country code
     * Validates Saudi phone number format (9 digits starting with 5)
     *
     * @param string|null $phone
     * @return string
     */
    private function cleanPhoneNumber($phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading zeros
        $phone = ltrim($phone, '0');

        // Remove country code if present
        if (str_starts_with($phone, '966')) {
            $phone = substr($phone, 3);
        }

        // Ensure we have a valid Saudi phone number (9 digits starting with 5)
        if (strlen($phone) === 9 && str_starts_with($phone, '5')) {
            return $phone;
        }

        // If phone doesn't match expected format, return empty (will trigger validation error)
        return '';
    }

    // MyFatoorah invoice items method removed in Paymob migration
}

