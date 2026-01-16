<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Myfatoorah\PaymentMyfatoorahApiV2;

class PaymentService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('myfatoorah');
    }

    /**
     * Initialize MyFatoorah payment for an order
     */
    public function initializeMyFatoorahPayment(Order $order, User $user, array $options = [])
    {
        try {
            $pay = new PaymentMyfatoorahApiV2(
                $this->config['api_key'],
                $this->config['test_mode'],
                $this->config['log_enabled'] ? $this->config['log_file'] : null
            );

            // Calculate final total and format items
            $items = $this->formatOrderItems($order);
            $finalTotal = (float) $order->total;

            $postFields = [
                'NotificationOption' => $this->config['notification_option'],
                'InvoiceValue'       => $finalTotal,
                'CustomerName'       => $user->name,
                'DisplayCurrencyIso' => $this->config['currency'],
                'MobileCountryCode'  => $this->config['country_code'],
                'CustomerMobile'     => ltrim($user->phone, '0'),
                'CallBackUrl'        => route('payment.success'),
                'ErrorUrl'           => route('payment.error'),
                'Language'           => $this->config['language'],
                'CustomerReference'  => $order->id,
                'UserDefinedField'   => 'order_payment',
                'CustomerEmail'      => $user->email,
                'InvoiceItems'       => $items,
            ];

            // Get payment gateway
            $gateway = $options['gateway'] ?? 'myfatoorah';

            // Resolve PaymentMethodId if gateway is not 'myfatoorah'
            if ($gateway !== 'myfatoorah') {
                $gateway = $this->resolvePaymentMethodId($gateway, $finalTotal, $pay, $this->config);
            }

            // Log the payment request
            Log::info('MyFatoorah payment request', [
                'order_id' => $order->id,
                'final_total' => $finalTotal,
                'gateway' => $gateway,
                'items_count' => count($items)
            ]);

            $data = $pay->getInvoiceURL($postFields, $gateway, $order->id);

            // Store payment information in order
            $paymentGatewayData = [
                'gateway' => $gateway,
                'invoice_id' => $data['invoiceId'],
                'invoice_url' => $data['invoiceURL'],
                'payment_request' => $postFields,
                'created_at' => now()->toISOString(),
            ];

            $order->update([
                'payment_gateway_data' => $paymentGatewayData
            ]);

            return [
                'status' => 'success',
                'invoiceURL' => $data['invoiceURL'],
                'invoiceId' => $data['invoiceId']
            ];

        } catch (\Exception $e) {
            Log::error('MyFatoorah payment initialization failed', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment gateway based on payment method
     */
    public function getPaymentGateway(PaymentMethod $paymentMethod, array $data): string
    {
        // If gateway is explicitly provided in data, use it
        if (isset($data['gateway'])) {
            return $data['gateway'];
        }

        // Map payment method to default gateway
        return match ($paymentMethod) {
            PaymentMethod::VISA => 'visa',
            PaymentMethod::MADA => 'mada',
            PaymentMethod::APPLE_PAY => 'applepay',
            PaymentMethod::GOOGLE_PAY => 'googlepay',
            default => 'myfatoorah',
        };
    }

    /**
     * Resolve payment method ID for specific gateways (same approach as WalletRechargeService)
     */
    private function resolvePaymentMethodId($gateway, $invoiceValue, $myfatoorahApi, $config)
    {
        try {
            // Get all available payment methods for this amount
            $paymentMethods = $myfatoorahApi->getVendorGateways($invoiceValue);

            // Map gateway names to PaymentMethodCode for lookup
            $gatewayCodeMap = [
                'visa' => 'visa',
                'mada' => 'mada',
                'applepay' => 'ap',
                'googlepay' => 'gp',
                'mastercard' => 'mc',
                'amex' => 'ae'
            ];

            $targetCode = $gatewayCodeMap[$gateway] ?? $gateway;

            // Find the matching payment method from available gateways
            foreach ($paymentMethods as $method) {
                if (isset($method->PaymentMethodCode) && $method->PaymentMethodCode === $targetCode) {
                    Log::info('Resolved PaymentMethodId', [
                        'gateway' => $gateway,
                        'target_code' => $targetCode,
                        'payment_method_id' => $method->PaymentMethodId,
                        'payment_method_name' => $method->PaymentMethodEn
                    ]);

                    return $method->PaymentMethodId;
                }
            }

            throw new Exception("Payment method not available: {$gateway}");

        } catch (Exception $e) {
            Log::warning('Failed to resolve PaymentMethodId, falling back to myfatoorah', [
                'gateway' => $gateway,
                'error' => $e->getMessage()
            ]);

            // Fallback to myfatoorah if resolution fails
            return 'myfatoorah';
        }
    }

    /**
     * Handle payment callback from MyFatoorah webhook
     */
    public function handlePaymentCallback(Request $request)
    {
        try {
            Log::info('MyFatoorah webhook received', $request->all());
            
            // Extract payment ID from webhook payload - handle nested Data structure
            $data = $request->all();
            $paymentId = null;
            
            // Extract PaymentId from webhook payload
            if (isset($data['Data']['PaymentId'])) {
                $paymentId = $data['Data']['PaymentId'];
            } elseif (isset($data['PaymentId'])) {
                $paymentId = $data['PaymentId'];
            }
            
            Log::info('Extracted PaymentId', ['paymentId' => $paymentId, 'data_keys' => array_keys($data), 'data_data_keys' => isset($data['Data']) ? array_keys($data['Data']) : []]);
            
            if (!$paymentId) {
                Log::error('Payment ID missing from webhook', $request->all());
                return [
                    'success' => false,
                    'message' => 'Payment ID is required'
                ];
            }
            
            $config = config('myfatoorah');
            $pay = new PaymentMyfatoorahApiV2(
                $config['api_key'],
                $config['test_mode'],
                $config['log_enabled'] ? $config['log_file'] : null
            );

            $responseData = $pay->getPaymentStatus($paymentId, 'PaymentId');
            $responseDataArr = json_decode(json_encode($responseData), true);
            
            if ($responseDataArr['focusTransaction']['TransactionStatus'] === 'Succss') {
                $orderId = $responseDataArr['CustomerReference'];
                $order = Order::findOrFail($orderId);

                // Get existing payment gateway data and update with response
                $paymentGatewayData = $order->payment_gateway_data ?? [];
                $paymentGatewayData['payment_response'] = $responseDataArr;
                $paymentGatewayData['payment_status'] = 'success';
                $paymentGatewayData['payment_id'] = $responseDataArr['focusTransaction']['PaymentId'] ?? null;
                $paymentGatewayData['transaction_id'] = $responseDataArr['focusTransaction']['TransactionId'] ?? null;
                $paymentGatewayData['updated_at'] = now()->toISOString();

                // Store payment details
                $order->update([
                    'payment_status' => 'success',
                    'payment_gateway_data' => $paymentGatewayData,
                ]);

                // Clear cart
                $this->processPostPaymentActions($order);

                return [
                    'success' => true,
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'message' => 'Payment processed successfully'
                ];
            } else {
                $orderId = $responseDataArr['CustomerReference'];
                $order = Order::findOrFail($orderId);
                
                if ($order) {
                    // Get existing payment gateway data and update with response
                    $paymentGatewayData = $order->payment_gateway_data ?? [];
                    $paymentGatewayData['payment_response'] = $responseDataArr;
                    $paymentGatewayData['payment_status'] = 'failed';
                    $paymentGatewayData['failure_reason'] = $responseDataArr['focusTransaction']['Error'] ?? 'Payment failed';
                    $paymentGatewayData['updated_at'] = now()->toISOString();

                    $order->update([
                        'payment_status' => 'failed',
                        'payment_gateway_data' => $paymentGatewayData,
                    ]);
                }
                
                return [
                    'success' => false,
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'message' => 'Payment failed'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Payment callback processing failed', [
                'error' => $e->getMessage(),
                'payment_id' => $request->paymentId ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process post-payment actions like cart clearing
     */
    private function processPostPaymentActions(Order $order)
    {
        // Clear cart
        $cartService = app(CartService::class);
        $cartService->clearCart($order->user);
    }

    /**
     * Format order items for MyFatoorah invoice
     */
    private function formatOrderItems(Order $order)
    {
        $items = [];
        $totalItemsValue = 0;

        Log::info('Starting order items calculation', [
            'order_id' => $order->id,
            'final_total' => $order->final_total,
            'vat_amount' => $order->vat_amount ?? 0,
            'coupon_amount' => $order->coupon_amount ?? 0,
            'delivery_fee' => $order->delivery_fee ?? 0,
            'wallet_deduction' => $order->wallet_deduction ?? 0

        ]);

        // Add products
        foreach ($order->items as $item) {
            $itemTotal = round($item->price * $item->quantity, 2);
            $totalItemsValue += $itemTotal;
            $itemName = null;
            // Try to get product name, fallback to item name, fallback to generic
            if (isset($item->product) && !empty($item->product->title)) {
                $itemName = $item->product->title;
            } elseif (!empty($item->name)) {
                $itemName = $item->name;
            } else {
                $itemName = __('apis.product');
            }
            $items[] = [
                'ItemName' => $itemName,
                'Quantity' => $item->quantity,
                'UnitPrice' => round($item->price, 2),
            ];
        }

        // Add delivery fee if applicable
        if (($order->delivery_fee ?? 0) > 0) {
            $fee = (float) $order->delivery_fee;
            $items[] = [
                'ItemName' => 'Delivery Fee',
                'Quantity' => 1,
                'UnitPrice' => round($fee, 2),
            ];
            $totalItemsValue += round($fee, 2);
        }

        // Add gift fee if applicable
        if (($order->gift_fee ?? 0) > 0) {
            $gift = (float) $order->gift_fee;
            $items[] = [
                'ItemName' => 'Gift Fee',
                'Quantity' => 1,
                'UnitPrice' => round($gift, 2),
            ];
            $totalItemsValue += round($gift, 2);
        }

        // Add VAT if applicable
        if (($order->vat_amount ?? 0) > 0) {
            $vat = (float) $order->vat_amount;
            $items[] = [
                'ItemName' => 'VAT',
                'Quantity' => 1,
                'UnitPrice' => round($vat, 2),
            ];
            $totalItemsValue += round($vat, 2);
        }

        // Add coupon discount if applicable (as a negative value)
        if (($order->coupon_amount ?? 0) > 0) {
            $couponAmount = round((float) $order->coupon_amount, 2);
            $items[] = [
                'ItemName' => 'Coupon Discount',
                'Quantity' => 1,
                'UnitPrice' => -$couponAmount,
            ];
            $totalItemsValue -= $couponAmount;
        }

        // Add wallet deduction if applicable (as a negative value)
        if (($order->wallet_deduction ?? 0) > 0) {
            $walletAmount = round((float) $order->wallet_deduction, 2);
            $items[] = [
                'ItemName' => 'Wallet Deduction',
                'Quantity' => 1,
                'UnitPrice' => -$walletAmount,
            ];
            $totalItemsValue -= $walletAmount;
        }



        // Ensure the sum of items matches the order total (InvoiceValue)
        $orderTotal = isset($order->total) ? (float)$order->total : 0.0;
        $totalItemsValue = (float) $totalItemsValue;
        $totalItemsValue = round($totalItemsValue, 2);
        $orderTotal = round($orderTotal, 2);
        $diff = round($orderTotal - $totalItemsValue, 2);
        if (abs($diff) > 0.01) { // Only adjust if difference is significant
            $items[] = [
                'ItemName' => 'Rounding Adjustment',
                'Quantity' => 1,
                'UnitPrice' => $diff,
            ];
            $totalItemsValue += $diff;
        }

        Log::info('Final calculation', [
            'calculated_total' => $totalItemsValue,
            'order_total' => $orderTotal,
            'difference' => abs($totalItemsValue - $orderTotal),
            'items' => $items
        ]);

        return $items;
    }
}
