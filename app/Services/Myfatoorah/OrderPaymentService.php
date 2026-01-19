<?php

namespace App\Services\Myfatoorah;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Myfatoorah\MyFatoorahService;

class OrderPaymentService
{
    protected $myfatoorahApi;
    protected $config;
    protected $orderService;

    public function __construct(OrderService $orderService = null)
    {
        $this->config = config('myfatoorah');
        $this->myfatoorahApi = new MyFatoorahService();
        $this->orderService = $orderService ?? app(OrderService::class);
    }

    /**
     * Create payment invoice for order
     */
    public function createOrderPaymentInvoice(Order $order, User $user, array $options = [])
    {
        try {
            // Ensure proper amount formatting
            $invoiceValue = (float) $order->total;

            // Get clean phone number
            $cleanPhone = $this->cleanPhoneNumber($user->phone ?? '');

            // Ensure we have required data
            if (empty($cleanPhone)) {
                throw new Exception('Valid phone number is required for payment');
            }

            if ($invoiceValue <= 0) {
                throw new Exception('Invalid payment amount');
            }

            $postFields = [
                'NotificationOption' => $this->config['notification_option'],
                'InvoiceValue' => $invoiceValue,
                'CustomerName' => $user->name ?? 'Customer',
                'DisplayCurrencyIso' => $this->config['currency'],
                'MobileCountryCode' => $this->config['country_code'],
                'CustomerMobile' => $cleanPhone,
                'CallBackUrl' => route('payment.success'),
                'ErrorUrl' => route('payment.error'),
                'Language' => $this->config['language'],
                'CustomerReference' => (string) $order->id, // Use order ID as reference
                'UserDefinedField' => 'order_payment', // Identify this as order payment
                'CustomerEmail' => $user->email ?? 'noemail@example.com',
                'InvoiceItems' => $this->buildInvoiceItems($order),
            ];

            // Log the payment request for debugging
            Log::info('Creating MyFatoorah order invoice', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $invoiceValue,
                'currency' => $this->config['currency'],
                'phone' => $cleanPhone,
                'test_mode' => $this->config['test_mode'],
                'gateway' => $options['gateway'] ?? 'myfatoorah'
            ]);

            // Get payment gateway
            $gateway = $options['gateway'] ?? 'myfatoorah';

            // Resolve PaymentMethodId if gateway is not 'myfatoorah'
            if ($gateway !== 'myfatoorah') {
                $gateway = $this->resolvePaymentMethodId($gateway, $invoiceValue, $this->myfatoorahApi);
            }

            // Create invoice
            if ($gateway !== 'myfatoorah') {
                $postFields['PaymentMethodId'] = $gateway;
                $result = $this->myfatoorahApi->executePayment($postFields);
            } else {
                $result = $this->myfatoorahApi->sendPayment($postFields);
            }

            // Update order with payment reference
            $order->update([
                'payment_reference' => $result['invoiceId'],
            ]);

            return [
                'success' => true,
                'invoice_url' => $result['invoiceURL'],
                'invoice_id' => $result['invoiceId'],
                'order_id' => $order->id,
            ];

        } catch (Exception $e) {
            Log::error('MyFatoorah order payment creation failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment invoice: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Build invoice items from order
     */
    private function buildInvoiceItems(Order $order)
    {
        $items = [];

        // Add order items
        foreach ($order->items as $item) {
            $items[] = [
                'ItemName' => $item->item->name ?? 'Product',
                'Quantity' => $item->quantity,
                'UnitPrice' => (float) $item->price,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add delivery fee if applicable
        if ($order->delivery_fee > 0) {
            $items[] = [
                'ItemName' => 'Delivery Fee',
                'Quantity' => 1,
                'UnitPrice' => (float) $order->delivery_fee,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add gift fee if applicable
        if (($order->gift_fee ?? 0) > 0) {
            $items[] = [
                'ItemName' => 'Gift Fee',
                'Quantity' => 1,
                'UnitPrice' => (float) $order->gift_fee,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add booking fee if applicable
        if ($order->booking_fee > 0) {
            $items[] = [
                'ItemName' => 'Booking Fee',
                'Quantity' => 1,
                'UnitPrice' => (float) $order->booking_fee,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add home service fee if applicable
        if ($order->home_service_fee > 0) {
            $items[] = [
                'ItemName' => 'Home Service Fee',
                'Quantity' => 1,
                'UnitPrice' => (float) $order->home_service_fee,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add discount as negative item if applicable
        if ($order->discount_amount > 0) {
            $items[] = [
                'ItemName' => 'Discount' . ($order->coupon_code ? ' (' . $order->coupon_code . ')' : ''),
                'Quantity' => 1,
                'UnitPrice' => -(float) $order->discount_amount, // Negative value for discount
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Add loyalty points discount as negative item if applicable
        if ($order->loyalty_points_used > 0) {
            $items[] = [
                'ItemName' => 'Loyalty Points Discount',
                'Quantity' => 1,
                'UnitPrice' => -(float) $order->loyalty_points_used, // Negative value for discount
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // If no items, add a generic order item
        if (empty($items)) {
            $items[] = [
                'ItemName' => 'Order #' . $order->order_number,
                'Quantity' => 1,
                'UnitPrice' => (float) $order->total,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];
        }

        // Calculate items total and ensure it matches order total
        $itemsTotal = collect($items)->sum(function($item) {
            return $item['Quantity'] * $item['UnitPrice'];
        });

        // Check if there's a discrepancy and add adjustment item if needed
        $difference = round($order->total - $itemsTotal, 2);
        if (abs($difference) > 0.01) { // If difference is more than 1 cent
            $items[] = [
                'ItemName' => 'Total Adjustment',
                'Quantity' => 1,
                'UnitPrice' => $difference,
                'Weight' => 0,
                'Width' => 0,
                'Height' => 0,
                'Depth' => 0,
            ];

            // Recalculate total after adjustment
            $itemsTotal = collect($items)->sum(function($item) {
                return $item['Quantity'] * $item['UnitPrice'];
            });
        }

        Log::info('MyFatoorah invoice items breakdown', [
            'order_id' => $order->id,
            'order_total' => $order->total,
            'items_total' => $itemsTotal,
            'difference' => $difference,
            'items_count' => count($items),
            'items' => $items
        ]);

        return $items;
    }

    /**
     * Verify payment status for order
     */
    public function verifyOrderPayment($paymentId, $orderId = null)
    {
        try {
            $responseData = $this->myfatoorahApi->getPaymentStatus($paymentId, 'PaymentId');

            if (!$responseData) {
                throw new Exception('No payment data found');
            }

            // Calculate focusTransaction (mimic old library behavior)
            $transactions = $responseData['InvoiceTransactions'] ?? [];
            $focusTransaction = null;
            
            // 1. Try to find by PaymentId
            foreach ($transactions as $t) {
                if (isset($t['PaymentId']) && $t['PaymentId'] == $paymentId) {
                    $focusTransaction = $t;
                    break;
                }
            }
            
            // 2. Fallback to last transaction
            if (!$focusTransaction && !empty($transactions)) {
                // Sort by date if needed, but usually API returns ordered or we assume last
                $focusTransaction = end($transactions);
            }

            $responseData['focusTransaction'] = $focusTransaction;
            
            // Map InvoiceStatus to focusTransaction if not present
            if ($focusTransaction && !isset($responseData['InvoiceStatus'])) {
                 $responseData['InvoiceStatus'] = $focusTransaction['TransactionStatus'] ?? 'Unknown';
            }

            // Convert to array for easier handling (already array from MyFatoorahService)
            $responseArray = $responseData;

            // Verify this is an order payment
            $userDefinedField = $responseArray['UserDefinedField'] ?? '';

            // Check if it's JSON (website orders) or simple string (API orders)
            $isOrderPayment = false;
            $orderDataFromField = null;

            // Try to decode as JSON first (website orders)
            $decoded = json_decode($userDefinedField, true);
            if (is_array($decoded) && isset($decoded['order_id'])) {
                $isOrderPayment = true;
                $orderDataFromField = $decoded;
                Log::info('Order payment detected from JSON UserDefinedField', [
                    'payment_id' => $paymentId,
                    'order_data' => $orderDataFromField
                ]);
            } elseif ($userDefinedField === 'order_payment') {
                // Simple string (API orders)
                $isOrderPayment = true;
                Log::info('Order payment detected from string UserDefinedField', [
                    'payment_id' => $paymentId,
                    'user_defined_field' => $userDefinedField
                ]);
            }

            if (!$isOrderPayment) {
                Log::warning('Invalid payment type in verification', [
                    'payment_id' => $paymentId,
                    'user_defined_field' => $userDefinedField,
                    'expected' => 'order_payment or JSON with order_id'
                ]);
                throw new Exception('Invalid payment type');
            }

            // Get order ID from customer reference (order_number for website, order_id for API)
            $orderIdFromResponse = $responseArray['CustomerReference'];

            // If we have order data from JSON, try to find by order_number first
            $order = null;
            if ($orderDataFromField && isset($orderDataFromField['order_number'])) {
                $order = Order::withTrashed()->where('order_number', $orderDataFromField['order_number'])->first();
                Log::info('Looking up order by order_number from UserDefinedField', [
                    'order_number' => $orderDataFromField['order_number'],
                    'found' => $order ? 'yes' : 'no'
                ]);
            }

            // If not found, try by CustomerReference (could be order_number or order_id)
            if (!$order) {
                // Try as order_number first
                $order = Order::withTrashed()->where('order_number', $orderIdFromResponse)->first();

                // If still not found, try as order_id
                if (!$order) {
                    $order = Order::withTrashed()->find($orderIdFromResponse);
                }

                Log::info('Looking up order by CustomerReference', [
                    'customer_reference' => $orderIdFromResponse,
                    'found' => $order ? 'yes' : 'no'
                ]);
            }

            if (!$order) {
                Log::error('Order not found in MyFatoorah callback', [
                    'order_id_from_response' => $orderIdFromResponse,
                    'payment_id' => $paymentId,
                    'customer_reference' => $responseArray['CustomerReference'] ?? 'not_set',
                    'user_defined_field' => $userDefinedField
                ]);
                throw new Exception('Order not found with reference: ' . $orderIdFromResponse);
            }

            // Verify order ID matches if provided
            if ($orderId && $order->order_number != $orderId && $order->id != $orderId) {
                Log::warning('Order ID mismatch in verification', [
                    'provided_order_id' => $orderId,
                    'found_order_id' => $order->id,
                    'found_order_number' => $order->order_number
                ]);
                throw new Exception('Order ID mismatch');
            }

            Log::info('Order found for MyFatoorah callback', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'is_trashed' => $order->trashed(),
                'payment_status' => $order->payment_status
            ]);

            // Check payment status
            if ($responseArray['focusTransaction']['TransactionStatus'] === 'Succss') {
                return $this->handleSuccessfulPayment($order, $responseArray);
            } else {
                return $this->handleFailedPayment($order, $responseArray);
            }

        } catch (Exception $e) {
            Log::error('MyFatoorah order payment verification failed', [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Order $order, array $responseData)
    {
        return DB::transaction(function () use ($order, $responseData) {
            // Use OrderService to properly handle payment confirmation (including soft delete restoration)
            $orderService = app(\App\Services\OrderService::class);
            $paymentReference = $responseData['focusTransaction']['PaymentId'];

            // This will restore the order if it's soft deleted and update status
            $orderService->confirmPayment($order, $paymentReference);



            // Order status is already updated by OrderService->confirmPayment()

           
            Log::info('Order payment successful', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_id' => $responseData['focusTransaction']['PaymentId'],
                'amount' => $order->total,
                'cart_cleared' => true
            ]);

            return [
                'success' => true,
                'message' => 'Payment successful',
                'order' => $order->fresh(),
                'payment_data' => $responseData
            ];
        });
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Order $order, array $responseData)
    {
        return DB::transaction(function () use ($order, $responseData) {
            // Use OrderService to properly handle payment failure (including soft delete restoration)
            $orderService = app(\App\Services\OrderService::class);
            $failureReason = $responseData['InvoiceError'] ?? 'Payment failed via MyFatoorah';

            // This will restore the order if it's soft deleted, update status, and restore quantities
            $orderService->handlePaymentFailure($order, $failureReason);

            Log::warning('Order payment failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $responseData['InvoiceStatus'],
                'error' => $responseData['InvoiceError'] ?? 'Unknown error',
                'quantities_restored' => true
            ]);

            return [
                'success' => false,
                'message' => 'Payment failed: ' . ($responseData['InvoiceError'] ?? 'Unknown error'),
                'order' => $order->fresh(),
                'payment_data' => $this->formatPaymentDataForResponse($responseData)
            ];
        });
    }



    /**
     * Format payment data for API response with proper currency display
     */
    private function formatPaymentDataForResponse(array $responseData)
    {
        // Create a clean response with SAR values for display
        $formattedData = $responseData;

        // Ensure currency fields show SAR
        if (isset($formattedData['InvoiceDisplayValue'])) {
            // Extract the numeric value and format as SAR
            $numericValue = (float) ($formattedData['InvoiceValue'] ?? 0);
            $formattedData['InvoiceDisplayValue'] = number_format($numericValue, 3) . ' SAR';
        }

        // Update transaction currency display
        if (isset($formattedData['InvoiceTransactions']) && is_array($formattedData['InvoiceTransactions'])) {
            foreach ($formattedData['InvoiceTransactions'] as &$transaction) {
                $transaction['PaidCurrency'] = 'SAR';
                $transaction['Currency'] = 'SAR';

                // Format transaction value display
                if (isset($transaction['TransationValue'])) {
                    $transaction['TransationValueDisplay'] = number_format((float) $transaction['TransationValue'], 3) . ' SAR';
                }
                if (isset($transaction['DueValue'])) {
                    $transaction['DueValueDisplay'] = number_format((float) $transaction['DueValue'], 3) . ' SAR';
                }
            }
        }

        // Update invoice items currency display
        if (isset($formattedData['InvoiceItems']) && is_array($formattedData['InvoiceItems'])) {
            foreach ($formattedData['InvoiceItems'] as &$item) {
                if (isset($item['UnitPrice'])) {
                    $item['UnitPriceDisplay'] = number_format((float) $item['UnitPrice'], 3) . ' SAR';
                }
            }
        }

        return $formattedData;
    }

    /**
     * Resolve PaymentMethodId from gateway name (same approach as WalletRechargeService)
     */
    private function resolvePaymentMethodId($gateway, $invoiceValue, $myfatoorahApi)
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
     * Clean phone number for MyFatoorah
     */
    private function cleanPhoneNumber($phone)
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

    /**
     * Get available payment gateways for orders
     */
    public function getAvailableGateways($amount = 0)
    {
        try {
            $gateways = $this->myfatoorahApi->getVendorGateways($amount, $this->config['currency']);

            // Filter to only order-supported gateways
            $supportedGateways = collect($gateways)->filter(function ($gateway) {
                $code = is_array($gateway) ? ($gateway['PaymentMethodCode'] ?? '') : ($gateway->PaymentMethodCode ?? '');
                return in_array($code, $this->config['course_gateways']); // Reuse same gateways
            });

            return [
                'success' => true,
                'gateways' => $supportedGateways->values()->toArray()
            ];

        } catch (Exception $e) {
            Log::error('Failed to get MyFatoorah order gateways', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get payment gateways',
                'gateways' => []
            ];
        }
    }
}
