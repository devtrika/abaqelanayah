<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Order\OrderPaymentService;
use App\Services\WalletRechargeService;
use App\Services\Paymob\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobController extends Controller
{
    public function webhook(Request $request, OrderPaymentService $orderPaymentService, WalletRechargeService $walletService)
    {
        try {
            $data = $request->all();
            Log::info('Paymob webhook received', $data);

            $hmacSecret = config('paymob.hmac_secret');
            $skip = (bool) config('paymob.skip_verification');
            if (!$skip && !$this->validateWebhookHmac($data, $hmacSecret)) {
                Log::warning('Invalid Paymob webhook HMAC');
                return response('Invalid signature', 401);
            }

            $success = false;
            $merchantOrderId = '';

            if (isset($data['obj'])) {
                $obj = $data['obj'];
                $success = (bool) ($obj['success'] ?? false);
                $merchantOrderId = (string) (($obj['order'] ?? [])['merchant_order_id'] ?? '');
            } elseif (isset($data['intention']) || isset($data['transaction'])) {
                $intention = (array) ($data['intention'] ?? []);
                $transaction = (array) ($data['transaction'] ?? []);
                $success = (bool) ($transaction['success'] ?? false);
                $merchantOrderId = (string) ($intention['special_reference'] ?? '');
            }
            $parsed = PaymobService::parseMerchantOrderId($merchantOrderId);

            if ($parsed['type'] === 'ord') {
                $order = Order::where('order_number', $parsed['ref'])->first();
                if (!$order) {
                    return response('Order not found', 404);
                }
                if ($success) {
                    $orderPaymentService->confirmPayment($order);
                } else {
                    $orderPaymentService->handlePaymentFailure($order, 'Paymob failed');
                }
            } elseif ($parsed['type'] === 'wal') {
                $transaction = Transaction::find($parsed['ref']);
                if (!$transaction) {
                    return response('Transaction not found', 404);
                }
                if ($success) {
                    $walletService->completeWalletRecharge($transaction);
                } else {
                    $transaction->update(['status' => 'failed']);
                }
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Paymob webhook error', ['error' => $e->getMessage()]);
            return response('Server error', 500);
        }
    }

    public function callback(Request $request)
    {
        $query = $request->query();
        Log::info('Paymob callback received', $query);
        $hmacSecret = config('paymob.hmac_secret');
        $skip = (bool) config('paymob.skip_verification');
        if (!$skip && !$this->validateCallbackHmac($query, $hmacSecret)) {
            return view('payment.fail');
        }

        $success = filter_var($query['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merchantOrderId = (string) ($query['merchant_order_id'] ?? '');
        $parsed = PaymobService::parseMerchantOrderId($merchantOrderId);

        // Resolve order by either explicit order_id query or by merchant_order_id ref (order_number)
        $explicitOrderId = $request->query('order_id');
        $orderModel = null;
        if ($parsed['type'] === 'ord') {
            if ($explicitOrderId) {
                $orderModel = \App\Models\Order::find($explicitOrderId);
            }
            if (!$orderModel && $parsed['ref']) {
                $orderModel = \App\Models\Order::where('order_number', $parsed['ref'])->first();
            }
        }
        if ($parsed['type'] === 'ord') {
            if ($parsed['origin'] === 'api-order') {
                return $success
                    ? view('payment.success', ['order_id' => $orderModel?->id])
                    : view('payment.fail');
            }
            // website checkout: keep path param as orderNumber, add order_id as additional query
            return $success
                ? redirect()->route('website.checkout.success', ['orderNumber' => $orderModel?->id ?? $parsed['ref']])
                : view('payment.fail');
        } elseif ($parsed['type'] === 'wal') {
            if ($parsed['origin'] === 'wallet-deposit') {
                return redirect()->route('website.wallet.index');
            }
            return $success ? view('payment.success') : view('payment.fail');
        }

        return $success ? view('payment.success') : view('payment.fail');
    }

    private function validateWebhookHmac(array $payload, ?string $secret): bool
    {
        try {
            if (!$secret) return false;
            $obj = $payload['obj'] ?? [];
            $keys = [
                'amount_cents','created_at','currency','error_occured','has_parent_transaction','obj.id','integration_id','is_3d_secure','is_auth','is_capture','is_refunded','is_standalone_payment','is_voided','order.id','owner','pending','source_data.pan','source_data.sub_type','source_data.type','success'
            ];
            $map = function(string $key) use ($obj) {
                switch ($key) {
                    case 'obj.id':
                        return (string) ($obj['id'] ?? '');
                    case 'order.id':
                        return (string) (($obj['order'] ?? [])['id'] ?? '');
                    case 'source_data.pan':
                        return (string) (($obj['source_data'] ?? [])['pan'] ?? '');
                    case 'source_data.sub_type':
                        return (string) (($obj['source_data'] ?? [])['sub_type'] ?? '');
                    case 'source_data.type':
                        return (string) (($obj['source_data'] ?? [])['type'] ?? '');
                    case 'error_occured':
                    case 'has_parent_transaction':
                    case 'is_3d_secure':
                    case 'is_auth':
                    case 'is_capture':
                    case 'is_refunded':
                    case 'is_standalone_payment':
                    case 'is_voided':
                    case 'pending':
                    case 'success':
                        return isset($obj[$key]) && $obj[$key] ? 'true' : 'false';
                    default:
                        return (string) ($obj[$key] ?? '');
                }
            };
            $toHash = '';
            foreach ($keys as $k) { $toHash .= $map($k); }
            $calcHmac = hash_hmac('sha512', $toHash, $secret);
            return strtolower($calcHmac) === strtolower((string) ($payload['hmac'] ?? ''));
        } catch (\Throwable $e) {
            Log::error('Paymob webhook HMAC validation exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function validateCallbackHmac(array $query, ?string $secret): bool
    {
        try {
            if (!$secret) return false;
            $keys = [
                'amount_cents','created_at','currency','error_occured','has_parent_transaction','id','integration_id','is_3d_secure','is_auth','is_capture','is_refunded','is_standalone_payment','is_voided','order.id','owner','pending','source_data.pan','source_data.sub_type','source_data.type','success'
            ];
            $map = function(string $key) use ($query) {
                if ($key === 'order.id') {
                    return (string) ($query['order.id'] ?? $query['order'] ?? '');
                }
                if ($key === 'source_data.pan') {
                    return (string) ($query['source_data.pan'] ?? $query['source_data_pan'] ?? '');
                }
                if ($key === 'source_data.sub_type') {
                    return (string) ($query['source_data.sub_type'] ?? $query['source_data_sub_type'] ?? '');
                }
                if ($key === 'source_data.type') {
                    return (string) ($query['source_data.type'] ?? $query['source_data_type'] ?? '');
                }
                if (in_array($key, ['error_occured','has_parent_transaction','is_3d_secure','is_auth','is_capture','is_refunded','is_standalone_payment','is_voided','pending','success'], true)) {
                    $val = $query[$key] ?? '';
                    if ($val === '' ) return '';
                    return filter_var($val, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
                }
                return (string) ($query[$key] ?? '');
            };
            $toHash = '';
            foreach ($keys as $k) { $toHash .= $map($k); }
            $calcHmac = hash_hmac('sha512', $toHash, $secret);
            return strtolower($calcHmac) === strtolower((string) ($query['hmac'] ?? ''));
        } catch (\Throwable $e) {
            Log::error('Paymob callback HMAC validation exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
