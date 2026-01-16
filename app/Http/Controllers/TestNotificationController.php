<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use App\Traits\Firebase;

class TestNotificationController extends Controller
{
    use Firebase;
    /**
     * Show the test notification page
     */
    public function index(): View
    {
        $tokens = Device::latest()->get();
        
        return view('test-notification', compact('tokens'));
    }
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'message_en' => 'required|string',
            'message_ar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->input('token');
        $titleEn = $request->input('title_en');
        $titleAr = $request->input('title_ar');
        $messageEn = $request->input('message_en');
        $messageAr = $request->input('message_ar');

        $data = [
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'body' => [
                'en' => $messageEn,
                'ar' => $messageAr,
            ],
            'type' => 'admin_notify',
        ];

        $result = $this->sendFcmNotification([$token], ['web'], $data, 'en', null);

        return response()->json([
            'success' => (bool)($result['success'] ?? false),
            'message_id' => $result['results'][0]['message_id'] ?? null,
            'result' => $result,
        ], ($result['success'] ?? false) ? 200 : 500);
    }
    /**
     * Send a test notification to a specific token
     */
   
}

