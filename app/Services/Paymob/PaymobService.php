<?php

namespace App\Services\Paymob;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected PaymobClient $client;
    protected array $config;

    public function __construct()
    {
        $this->client = new PaymobClient();
        $this->config = config('paymob');
    }

    public function createOrderPayment(Order $order, string $origin = 'website-checkout'): array
    {
        $amountCents = (int) round(((float) $order->total) * 100);
        $merchantOrderId = $this->composeMerchantOrderId('ord', $origin, $order->order_number);
        $notificationUrl = (string) ($this->config['notification_url'] ?? route('payments.paymob.webhook'));
        $redirectionUrl = (string) ($this->config['redirection_url'] ?? route('payments.paymob.callback'));

        // Determine payment methods
        $paymentMethods = $this->config['card_integration_ids'] ?? [ (int) $this->config['card_integration_id'] ];
        
        // Filter payment methods if a specific method is selected and mapped
        if ($order->payment_method_id && isset($this->config['payment_method_map'][$order->payment_method_id])) {
            $mappedId = (int) $this->config['payment_method_map'][$order->payment_method_id];
            if ($mappedId > 0) {
                $paymentMethods = [$mappedId];
            }
        }

        Log::info('Paymob Intention Creation', [
            'order_id' => $order->id,
            'payment_method_id' => $order->payment_method_id,
            'resolved_integration_ids' => $paymentMethods,
            'all_configured_ids' => $this->config['card_integration_ids'] ?? []
        ]);

        $intentionResp = $this->client->createIntention([
            'amount' => $amountCents,
            'currency' => $this->config['currency'] ?? 'SAR',
            'payment_methods' => $paymentMethods,
            'billing_data' => $this->buildBillingDataFromOrder($order),
            'special_reference' => $merchantOrderId,
            'notification_url' => $notificationUrl,
            'redirection_url' => $redirectionUrl,
            'extras' => ['origin' => $origin],
        ]);

        $clientSecret = (string) ($intentionResp['client_secret'] ?? '');
        $url = $this->client->buildUnifiedCheckoutUrl($clientSecret);

        return [
            'payment_url' => $url,
            'intention_id' => $intentionResp['id'] ?? null,
            'client_secret' => $clientSecret,
            'merchant_order_id' => $merchantOrderId,
        ];
    }

    public function createWalletRechargePayment(Transaction $transaction, string $origin = 'wallet-deposit'): array
    {
        $amountCents = (int) round(((float) $transaction->amount) * 100);
        $merchantOrderId = $this->composeMerchantOrderId('wal', $origin, (string) $transaction->id);
        $notificationUrl = (string) ($this->config['notification_url'] ?? route('payments.paymob.webhook'));
        $redirectionUrl = (string) ($this->config['redirection_url'] ?? route('payments.paymob.callback'));
        $intentionResp = $this->client->createIntention([
            'amount' => $amountCents,
            'currency' => $this->config['currency'] ?? 'SAR',
            'payment_methods' => $this->config['card_integration_ids'] ?? [ (int) $this->config['card_integration_id'] ],
            'billing_data' => $this->buildBillingDataFromTransaction($transaction),
            'special_reference' => $merchantOrderId,
            'notification_url' => $notificationUrl,
            'redirection_url' => $redirectionUrl,
            'extras' => ['origin' => $origin],
        ]);

        $clientSecret = (string) ($intentionResp['client_secret'] ?? '');
        $url = $this->client->buildUnifiedCheckoutUrl($clientSecret);

        return [
            'payment_url' => $url,
            'intention_id' => $intentionResp['id'] ?? null,
            'client_secret' => $clientSecret,
            'merchant_order_id' => $merchantOrderId,
        ];
    }

    public static function parseMerchantOrderId(string $merchantOrderId): array
    {
        $parts = explode('-', $merchantOrderId);
        $type = $parts[0] ?? '';
        $ref = $parts[count($parts) - 1] ?? '';
        $origin = '';
        if (count($parts) > 2) {
            $origin = implode('-', array_slice($parts, 1, -1));
        }
        return [
            'type' => $type,
            'origin' => $origin,
            'ref' => $ref,
        ];
    }

    private function composeMerchantOrderId(string $type, string $origin, string $ref): string
    {
        return $type . '-' . $origin . '-' . $ref;
    }

    private function buildBillingDataFromOrder(Order $order): array
    {
        $user = $order->user;
        return [
            'apartment' => 'NA',
            'email' => $user->email ?? 'noemail@example.com',
            'floor' => 'NA',
            'first_name' => $user->name ?? 'Customer',
            'street' => 'NA',
            'building' => 'NA',
            'phone_number' => $user->phone ?? '0000000000',
            'shipping_method' => 'NA',
            'postal_code' => 'NA',
            'city' => 'NA',
            'country' => 'SA',
            'last_name' => '.',
            'state' => 'NA',
        ];
    }

    private function buildBillingDataFromTransaction(Transaction $transaction): array
    {
        $user = $transaction->user;
        return [
            'apartment' => 'NA',
            'email' => $user->email ?? 'noemail@example.com',
            'floor' => 'NA',
            'first_name' => $user->name ?? 'Customer',
            'street' => 'NA',
            'building' => 'NA',
            'phone_number' => $user->phone ?? '0000000000',
            'shipping_method' => 'NA',
            'postal_code' => 'NA',
            'city' => 'NA',
            'country' => 'SA',
            'last_name' => '.',
            'state' => 'NA',
        ];
    }
}
