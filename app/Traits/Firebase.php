<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait Firebase
{
    public function sendFcmNotification($tokens, $types = [], $data = [], $lang = 'ar', $extra = null)
    {
        if (empty($tokens)) {
            return ['success' => false, 'error' => 'No tokens provided'];
        }

        $serverKey = config('services.firebase.server_key') ?? config('firebase.server_key') ?? env('FIREBASE_SERVER_KEY');
        
        if (!$serverKey) {
            Log::warning('Firebase server key not configured');
            return ['success' => false, 'error' => 'Firebase server key not configured'];
        }

        $title = '';
        if (isset($data['title'])) {
            $title = is_array($data['title']) ? ($data['title'][$lang] ?? $data['title']['ar'] ?? '') : $data['title'];
        }
        
        $body = '';
        if (isset($data['body'])) {
            $body = is_array($data['body']) ? ($data['body'][$lang] ?? $data['body']['ar'] ?? '') : $data['body'];
        }

        $payload = [
            'registration_ids' => is_array($tokens) ? $tokens : [$tokens],
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'success_count' => $responseData['success'] ?? 0,
                    'failure_count' => $responseData['failure'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
