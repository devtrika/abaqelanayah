<?php

namespace App\Notifications\Channels;

use App\Models\UserDeviceToken;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        // Respect delivery users' notification preference
        if ($notifiable instanceof \App\Models\User && $notifiable->type === 'delivery' && !$notifiable->is_notify) {
            Log::info('Delivery user disabled notifications, skipping Firebase', ['user_id' => $notifiable->id]);
            return;
        }

        $firebaseData = $notification->toFirebase($notifiable);

        if (empty($firebaseData)) {
            return;
        }

        // Get active device tokens for the user
        $deviceTokens = $notifiable->activeDeviceTokens()->pluck('device_token')->toArray();
        
        if (empty($deviceTokens)) {
            Log::info('No device tokens found for user', ['user_id' => $notifiable->id]);
            return;
        }

        $responses = [];
        $failedTokens = [];

        foreach ($deviceTokens as $token) {
            try {
                $response = $this->sendToToken($token, $firebaseData);
                $responses[] = $response;
                
                // Mark token as used
                UserDeviceToken::where('device_token', $token)->first()?->markAsUsed();
                
            } catch (Exception $e) {
                Log::error('Firebase notification failed', [
                    'token' => $token,
                    'error' => $e->getMessage(),
                    'user_id' => $notifiable->id
                ]);
                
                $failedTokens[] = $token;
                
                // Deactivate invalid tokens
                if ($this->isInvalidToken($e)) {
                    UserDeviceToken::where('device_token', $token)->first()?->deactivate();
                }
            }
        }

        // Store response data in notification
        if (method_exists($notification, 'setFirebaseResponse')) {
            $notification->setFirebaseResponse([
                'responses' => $responses,
                'failed_tokens' => $failedTokens,
                'sent_at' => now()->toISOString()
            ]);
        }

        return $responses;
    }

    /**
     * Send notification to a specific token.
     */
    protected function sendToToken(string $token, array $data): array
    {
        $serverKey = config('services.firebase.server_key');
        
        if (empty($serverKey)) {
            throw new Exception('Firebase server key not configured');
        }

        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
                'icon' => $data['icon'] ?? config('app.url') . '/favicon.ico',
                'click_action' => $data['click_action'] ?? null,
                'sound' => $data['sound'] ?? 'default',
            ],
            'data' => $data['data'] ?? [],
            'priority' => $data['priority'] ?? 'high',
            'content_available' => true,
        ];

        // Add iOS specific settings
        if (isset($data['apns'])) {
            $payload['apns'] = $data['apns'];
        }

        // Add Android specific settings
        if (isset($data['android'])) {
            $payload['android'] = $data['android'];
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        if (!$response->successful()) {
            throw new Exception('Firebase request failed: ' . $response->body());
        }

        $responseData = $response->json();
        
        if (isset($responseData['failure']) && $responseData['failure'] > 0) {
            $error = $responseData['results'][0]['error'] ?? 'Unknown error';
            throw new Exception('Firebase notification failed: ' . $error);
        }

        return $responseData;
    }

    /**
     * Check if the error indicates an invalid token.
     */
    protected function isInvalidToken(Exception $e): bool
    {
        $invalidTokenErrors = [
            'InvalidRegistration',
            'NotRegistered',
            'MismatchSenderId',
            'InvalidPackageName'
        ];

        foreach ($invalidTokenErrors as $error) {
            if (str_contains($e->getMessage(), $error)) {
                return true;
            }
        }

        return false;
    }
}
