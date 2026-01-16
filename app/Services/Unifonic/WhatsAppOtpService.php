<?php

namespace App\Services\Unifonic;

use App\Models\User;

class WhatsAppOtpService
{
    protected UnifonicWhatsAppClient $client;
    protected string $template;
    protected string $language;

    public function __construct()
    {
        $this->client = new UnifonicWhatsAppClient();
        $conf = config('unifonic.whatsapp');
        $this->template = (string) ($conf['template'] ?? 'sandbox_account_update');
        $this->language = (string) ($conf['language'] ?? 'en');
    }

    public function send(User $user, string $code): array
    {
        return $this->sendToPhone($user->full_phone, $code);
    }

    public function sendToPhone(string $phone, string $code): array
    {
        $bodyParams = ['code' => $code];
        
        // Prepare button parameters for Authentication templates
        // Authentication templates typically require the OTP code as a button parameter (for 'copy code' or 'url')
        // We add it here to ensure compliance with Auth template requirements.
        // Even if the template is not Auth, if it doesn't use buttons, this might be ignored or cause an error depending on template config.
        // But given this is an OTP service, we prioritize Auth template support.
        $buttonParams = [
            [
                'type' => 'url', // Must be 'url' even for Copy Code buttons as per Unifonic API requirement
                'value' => $code,
                'index' => 0
            ]
        ];

        // Use the method that supports buttons
        return $this->client->sendTemplateMessageWithButtons($phone, $this->template, $bodyParams, $buttonParams, $this->language);
    }
}

