<?php

namespace App\Services\Unifonic;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnifonicWhatsAppClient
{
    protected string $baseUrl;
    protected string $publicId;
    protected string $secret;
    protected string $messagesEndpoint;

    public function __construct()
    {
        $config = config('unifonic');
        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'https://apis.unifonic.com'), '/');
        $this->publicId = (string) ($config['public_id'] ?? '');
        $this->secret = (string) ($config['secret'] ?? '');
        $this->messagesEndpoint = (string) ($config['whatsapp']['messages_endpoint'] ?? '/v1/messages');
    }

    public function sendTemplateMessage(string $to, string $template, array $parameters = [], string $language = 'en'): array
    {
        // Ensure phone number starts with +
        if (!str_starts_with($to, '+')) {
            $to = '+' . $to;
        }

        $components = [];

        // Body parameters
        if (!empty($parameters)) {
            $bodyParams = [];
            foreach ($parameters as $value) {
                $bodyParams[] = ['type' => 'text', 'text' => (string) $value];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $bodyParams
            ];
        }

        $payload = [
            'recipient' => [
                'contact' => $to,
                'channel' => 'whatsapp',
            ],
            'content' => [
                'type' => 'template',
                'name' => $template,
                'language' => ['code' => $language],
                'components' => $components
            ]
        ];

        $headers = [
            'PublicId' => $this->publicId,
            'secret' => $this->secret,
            'Accept' => 'application/json',
        ];

        Log::debug('Sending WhatsApp template', ['payload' => $payload]);

        $resp = Http::withHeaders($headers)
            ->withoutVerifying()
            ->post($this->baseUrl . $this->messagesEndpoint, $payload);

        if (!$resp->successful()) {
            Log::error('Unifonic WhatsApp send failed', [
                'status' => $resp->status(),
                'body' => $resp->body(),
                'payload' => $payload
            ]);
            throw new \Exception('Unifonic WhatsApp send failed: ' . $resp->body());
        }

        Log::debug('Unifonic WhatsApp send success', ['response' => $resp->json()]);

        return $resp->json();
    }

    public function sendTemplateMessageWithButtons(
        string $to, 
        string $template, 
        array $bodyParameters = [], 
        array $buttonParameters = [],
        string $language = 'en'
    ): array
    {
        if (!str_starts_with($to, '+')) {
            $to = '+' . $to;
        }

        $components = [];

        // Body parameters
        if (!empty($bodyParameters)) {
            $bodyParams = [];
            foreach ($bodyParameters as $value) {
                $bodyParams[] = ['type' => 'text', 'text' => (string) $value];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $bodyParams
            ];
        }

        // Button parameters (options)
        if (!empty($buttonParameters)) {
            $optionParams = [];
            foreach ($buttonParameters as $index => $button) {
                // Map generic button structure to Unifonic options structure
                // Expecting $button to have 'type' (url/quickReply), 'value' (url/payload), 'index'
                $optionParams[] = [
                    'subType' => $button['type'] ?? 'url', 
                    'value' => $button['url'] ?? $button['value'] ?? '', 
                    'index' => $button['index'] ?? $index
                ];
            }
            $components[] = [
                'type' => 'options',
                'parameters' => $optionParams
            ];
        }

        $payload = [
            'recipient' => [
                'contact' => $to,
                'channel' => 'whatsapp',
            ],
            'content' => [
                'type' => 'template',
                'name' => $template,
                'language' => ['code' => $language],
                'components' => $components
            ]
        ];

        $headers = [
            'PublicId' => $this->publicId,
            'secret' => $this->secret,
            'Accept' => 'application/json',
        ];

        Log::debug('Sending WhatsApp template with buttons', ['payload' => $payload]);

        $resp = Http::withHeaders($headers)
            ->withoutVerifying()
            ->post($this->baseUrl . $this->messagesEndpoint, $payload);

        if (!$resp->successful()) {
            Log::error('Unifonic WhatsApp send failed', [
                'status' => $resp->status(),
                'body' => $resp->body(),
                'payload' => $payload
            ]);
            throw new \Exception('Unifonic WhatsApp send failed: ' . $resp->body());
        }

        Log::debug('Unifonic WhatsApp send success', ['response' => $resp->json()]);

        return $resp->json();
    }

    public function getMessageStatus(string $messageId): array
    {
        // Headers with capitalized Secret just in case, or try both if unsure.
        // But let's try the /v1/messages/status endpoint directly.
        $headers = [
            'PublicId' => $this->publicId,
            'Secret' => $this->secret, // Capital 'S' based on docs
            'Accept' => 'application/json',
        ];

        // Using the query param endpoint
        $url = $this->baseUrl . '/v1/messages/status';
        
        Log::debug('Checking Unifonic message status', ['url' => $url, 'messageId' => $messageId]);

        $resp = Http::withHeaders($headers)
            ->withoutVerifying()
            ->get($url, ['messageIds' => $messageId]);

        if (!$resp->successful()) {
            // If failed, try with lowercase 'secret' just in case
             $headers['secret'] = $this->secret;
             unset($headers['Secret']);
             
             $resp = Http::withHeaders($headers)
                ->withoutVerifying()
                ->get($url, ['messageIds' => $messageId]);
        }

        if (!$resp->successful()) {
            Log::error('Unifonic Get Status failed', [
                'status' => $resp->status(),
                'body' => $resp->body(),
                'messageId' => $messageId
            ]);
            throw new \Exception('Unifonic Get Status failed: ' . $resp->body());
        }

        return $resp->json();
    }
}