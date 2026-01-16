<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnifonicWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp status updates from Unifonic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        try {
            // Log the entire payload for debugging/auditing
            Log::info('Unifonic WhatsApp Webhook received:', $request->all());

            // Optional: Verify Secret (Unifonic might send it in headers or payload)
            // Typically Unifonic sends an 'X-Unifonic-Signature' or similar, but docs vary.
            // If you need to verify based on the secret you provided:
            // $secret = config('unifonic.whatsapp.webhook_secret');
            // ... verification logic ...

            $data = $request->all();

            // Example: Check if it's a status update
            if (isset($data['messageId']) && isset($data['status'])) {
                // TODO: Update your database records based on messageId
                // $log = WhatsAppLog::where('message_id', $data['messageId'])->first();
                // if ($log) {
                //     $log->update(['status' => $data['status']]);
                // }
            }

            // Return 200 OK to acknowledge receipt
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Unifonic Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle incoming Voice Call status updates from Unifonic.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleVoice(Request $request)
    {
        try {
            Log::info('Unifonic Voice Webhook received:', $request->all());

            $data = $request->all();

            // Voice payload typically contains: callSid, status, duration, etc.
            if (isset($data['callSid']) && isset($data['status'])) {
                 // TODO: Handle voice call status update
                 // e.g. Log call duration, status (COMPLETED, BUSY, etc.)
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Unifonic Voice Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
