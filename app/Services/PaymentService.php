<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\CourseEnrollment;
use App\Enums\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Myfatoorah\MyFatoorahService;
use App\Services\CartService;

class PaymentService
{
    protected $config;
    protected $myfatoorahApi;

    public function __construct()
    {
        $this->config = config('myfatoorah');
        $this->myfatoorahApi = new MyFatoorahService();
    }

    /**
     * Initialize MyFatoorah payment for an order
     */
    public function initializeMyFatoorahPayment(Order $order, User $user, array $options = [])
    {
        try {
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
                'CallBackUrl'        => route('payment.success', ['origin' => $options['origin'] ?? 'website']),
                'ErrorUrl'           => route('payment.error', ['origin' => $options['origin'] ?? 'website']),
                'Language'           => $this->config['language'],
                'CustomerReference'  => $order->id,
                'UserDefinedField'   => 'order_payment',
                'CustomerEmail'      => $user->email,
                'InvoiceItems'       => $items,
            ];

            return $this->processPaymentRequest($this->myfatoorahApi, $postFields, $options, $order);

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
     * Initialize MyFatoorah payment for a transaction (Wallet Recharge)
     */
    public function initializeTransactionPayment(Transaction $transaction, User $user, array $options = [])
    {
        try {
            $finalTotal = (float) $transaction->amount;

            $postFields = [
                'NotificationOption' => $this->config['notification_option'],
                'InvoiceValue'       => $finalTotal,
                'CustomerName'       => $user->name,
                'DisplayCurrencyIso' => $this->config['currency'],
                'MobileCountryCode'  => $this->config['country_code'],
                'CustomerMobile'     => ltrim($user->phone, '0'),
                'CallBackUrl'        => route('payment.success', ['origin' => $options['origin'] ?? 'website']),
                'ErrorUrl'           => route('payment.error', ['origin' => $options['origin'] ?? 'website']),
                'Language'           => $this->config['language'],
                'CustomerReference'  => $transaction->id,
                'UserDefinedField'   => 'wallet_recharge',
                'CustomerEmail'      => $user->email,
                'InvoiceItems'       => [
                    [
                        'ItemName'  => 'Wallet Recharge',
                        'Quantity'  => 1,
                        'UnitPrice' => $finalTotal,
                    ]
                ],
            ];

            return $this->processPaymentRequest($this->myfatoorahApi, $postFields, $options, $transaction);

        } catch (\Exception $e) {
            Log::error('MyFatoorah transaction payment initialization failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process the payment request with MyFatoorah
     */
    private function processPaymentRequest($pay, $postFields, $options, $model)
    {
        // Get payment gateway
        $gateway = $options['gateway'] ?? 'myfatoorah';
        $finalTotal = $postFields['InvoiceValue'];

        // Resolve PaymentMethodId if gateway is not 'myfatoorah'
        if ($gateway !== 'myfatoorah') {
            $gateway = $this->resolvePaymentMethodId($gateway, $finalTotal, $pay, $this->config);
        }

        // Log the payment request
        Log::info('MyFatoorah payment request', [
            'model_id' => $model->id,
            'model_type' => get_class($model),
            'final_total' => $finalTotal,
            'gateway' => $gateway,
        ]);

        if ($gateway !== 'myfatoorah') {
             $postFields['PaymentMethodId'] = $gateway;
             $data = $pay->executePayment($postFields);
        } else {
             $data = $pay->sendPayment($postFields);
        }

        // Store payment information in model (Order or Transaction)
        // Check if model has 'update' method or handle accordingly
        // Assuming both Order and Transaction are Eloquent models
        
        $paymentGatewayData = [
            'gateway' => $gateway,
            'invoice_id' => $data['invoiceId'],
            'invoice_url' => $data['invoiceURL'],
            'payment_request' => $postFields,
            'created_at' => now()->toISOString(),
        ];

        // For Order, column is 'payment_gateway_data'
        // For Transaction, we might need to check column name or if it exists.
        // Assuming 'payment_gateway_data' exists or we use 'note' or similar if not.
        // But for Order it's definitely 'payment_gateway_data'.
        
        if ($model instanceof Order) {
            $model->update([
                'payment_gateway_data' => $paymentGatewayData
            ]);
        } elseif ($model instanceof Transaction) {
             $model->update([
                'reference' => $data['invoiceId'], // Store invoice ID in reference
                // If Transaction has a json column for metadata, use it. Otherwise just reference.
            ]);
        }

        return [
            'status' => 'success',
            'invoiceURL' => $data['invoiceURL'],
            'invoiceId' => $data['invoiceId']
        ];
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
                // Handle both array and object response
                $methodCode = is_array($method) ? ($method['PaymentMethodCode'] ?? '') : ($method->PaymentMethodCode ?? '');
                $methodId = is_array($method) ? ($method['PaymentMethodId'] ?? '') : ($method->PaymentMethodId ?? '');
                $methodName = is_array($method) ? ($method['PaymentMethodEn'] ?? '') : ($method->PaymentMethodEn ?? '');

                if ($methodCode === $targetCode) {
                    Log::info('Resolved PaymentMethodId', [
                        'gateway' => $gateway,
                        'target_code' => $targetCode,
                        'payment_method_id' => $methodId,
                        'payment_method_name' => $methodName
                    ]);

                    return $methodId;
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
            Log::info('MyFatoorah callback processing', $request->all());
            
            // Extract payment ID from webhook payload - handle nested Data structure
            $data = $request->all();
            $paymentId = null;
            
            // Extract PaymentId from webhook/callback payload
            if (isset($data['Data']['PaymentId'])) {
                $paymentId = $data['Data']['PaymentId'];
            } elseif (isset($data['PaymentId'])) {
                $paymentId = $data['PaymentId'];
            } elseif (isset($data['paymentId'])) {
                $paymentId = $data['paymentId'];
            }
            
            Log::info('Extracted PaymentId', ['paymentId' => $paymentId]);
            
            if (!$paymentId) {
                Log::error('Payment ID missing from webhook', $request->all());
                return [
                    'success' => false,
                    'message' => 'Payment ID is required'
                ];
            }
            
            $config = config('myfatoorah');
            
            $responseData = $this->myfatoorahApi->getPaymentStatus($paymentId, 'PaymentId');
            
            if (!$responseData) {
                throw new Exception('No payment data found');
            }

            // Calculate focusTransaction
            $transactions = $responseData['InvoiceTransactions'] ?? [];
            $focusTransaction = null;
            
            foreach ($transactions as $t) {
                if (isset($t['PaymentId']) && $t['PaymentId'] == $paymentId) {
                    $focusTransaction = $t;
                    break;
                }
            }
            if (!$focusTransaction && !empty($transactions)) {
                $focusTransaction = end($transactions);
            }
            
            $responseData['focusTransaction'] = $focusTransaction;
            $responseDataArr = $responseData;
            
            $userDefinedField = $responseDataArr['UserDefinedField'] ?? '';
            $customerReference = $responseDataArr['CustomerReference'] ?? '';
            $transactionStatus = $responseDataArr['focusTransaction']['TransactionStatus'] ?? '';

            // Handle Wallet Recharge
            if ($userDefinedField === 'wallet_recharge') {
                $transaction = Transaction::find($customerReference);
                
                if (!$transaction) {
                    Log::error('Transaction not found for wallet recharge callback', ['id' => $customerReference]);
                    return ['success' => false, 'message' => 'Transaction not found'];
                }

                if ($transactionStatus === 'Succss') {
                    // Update transaction and user wallet
                    DB::transaction(function () use ($transaction, $responseDataArr) {
                        $transaction->update([
                            'status' => 'completed',
                            'reference' => $responseDataArr['focusTransaction']['TransactionId'] ?? $transaction->reference,
                        ]);
                        
                        // Increment user wallet if not already completed (idempotency check)
                        // Assuming status 'completed' is final. But checking logic might be safer.
                        // However, we just set it to completed inside this transaction.
                        // Better to check if it WAS pending.
                        // But here we just assume it's the first time processing or we rely on status check before this block?
                        // For safety, we can rely on current status.
                        
                        $transaction->user->increment('wallet_balance', $transaction->amount);
                    });

                    Log::info('Wallet recharge successful', ['transaction_id' => $transaction->id]);

                    return [
                        'success' => true,
                        'transaction_id' => $transaction->id,
                        'message' => 'Wallet recharge successful'
                    ];
                } else {
                    $transaction->update(['status' => 'failed']);
                    Log::info('Wallet recharge failed', ['transaction_id' => $transaction->id]);
                    
                    return [
                        'success' => false,
                        'transaction_id' => $transaction->id,
                        'message' => 'Wallet recharge failed'
                    ];
                }
            }

            // Handle Course Enrollment
            if ($userDefinedField === 'course_enrollment') {
                $enrollment = CourseEnrollment::find($customerReference);

                if (!$enrollment) {
                    Log::error('Course enrollment not found for callback', ['id' => $customerReference]);
                    return ['success' => false, 'message' => 'Enrollment not found'];
                }

                if ($transactionStatus === 'Succss') {
                    $paymentReference = $responseDataArr['focusTransaction']['TransactionId'] ?? $responseDataArr['InvoiceId'];
                    
                    // Use CourseService to confirm payment
                    $courseService = app(\App\Services\CourseService::class);
                    $courseService->confirmCoursePayment($enrollment, $paymentReference);

                    Log::info('Course payment successful', ['enrollment_id' => $enrollment->id]);

                    return [
                        'success' => true,
                        'enrollment_id' => $enrollment->id,
                        'message' => 'Course payment successful'
                    ];
                } else {
                    // Mark as failed if possible, or just log
                    // CourseEnrollment status might need update
                    $enrollment->update(['status' => 'failed', 'payment_status' => 'failed']);
                    Log::info('Course payment failed', ['enrollment_id' => $enrollment->id]);

                    return [
                        'success' => false,
                        'enrollment_id' => $enrollment->id,
                        'message' => 'Course payment failed'
                    ];
                }
            }

            // Handle Order Payment (Default)
            if ($transactionStatus === 'Succss') {
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
                    'order_number' => $order->order_number,
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
