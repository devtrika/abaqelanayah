<?php

namespace App\Services\Paymob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobClient
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $publicKey;

    public function __construct()
    {
        $config = config('paymob');
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://ksa.paymob.com'), '/');
        $this->secretKey = (string) ($config['secret_key'] ?? '');
        $this->publicKey = (string) ($config['public_key'] ?? '');
    }

    public function createIntention(array $payload): array
    {
        $resp = Http::withHeaders([
            'Authorization' => 'Token ' . $this->secretKey,
        ])->withoutVerifying()->post($this->baseUrl . '/v1/intention/', $payload);
        if (!$resp->successful()) {
            Log::error('Paymob create intention failed', ['status' => $resp->status(), 'body' => $resp->body(), 'payload' => $payload]);
            throw new \Exception('Paymob create intention failed');
        }
        return $resp->json();
    }

    public function buildUnifiedCheckoutUrl(string $clientSecret): string
    {
        $base = $this->baseUrl . '/unifiedcheckout/';
        $pub = urlencode($this->publicKey);
        $sec = urlencode($clientSecret);
        return $base . "?publicKey={$pub}&clientSecret={$sec}";
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
