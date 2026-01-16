<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configuration
$appSid = env('UNIFONIC_PUBLIC_ID'); // Usually matches AppsId for Voice
$webhookUrl = 'https://cea6448eb509.ngrok-free.app/api/webhooks/unifonic/voice'; // Update this if your ngrok URL changes
$voiceWebhookEndpoint = 'https://voice.unifonic.com/v1/providers/webhook';

echo "Registering Voice Webhook...\n";
echo "AppSid: $appSid\n";
echo "Webhook URL: $webhookUrl\n";

$payload = [
    'url' => $webhookUrl,
    'basicAuthNeeded' => false,
    // 'username' => 'XX',
    // 'password' => 'XXX'
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $voiceWebhookEndpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    '_AppsId: ' . $appSid,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "HTTP Status Code: $httpCode\n";
    echo "Response: $response\n";
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "Webhook registered successfully!\n";
    } else {
        echo "Failed to register webhook.\n";
    }
}
