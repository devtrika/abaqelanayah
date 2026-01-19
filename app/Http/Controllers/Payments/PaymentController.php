<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle payment success/error callback from MyFatoorah (redirected from gateway)
     */
    public function callback(Request $request)
    {
        $paymentId = $request->query('paymentId');
        $origin = $request->query('origin', 'website');
        
        Log::info('Payment callback received', $request->all());

        if (!$paymentId) {
             return redirect()->route('payment.error');
        }

        $result = $this->paymentService->handlePaymentCallback($request);

        // Handle API origin
        if (in_array($origin, ['api', 'api-wallet', 'api-order'])) {
            if ($result['success']) {
                return redirect()->route('payment.success.view');
            }
            return redirect()->route('payment.error');
        }

        // Handle Website origin
        if ($result['success']) {
            // If it's a wallet recharge transaction
            if (isset($result['transaction_id'])) {
                 return redirect()->route('website.wallet.index')->with('success', __('messages.payment_success'));
            }

            // If it's a course enrollment
            if (isset($result['enrollment_id'])) {
                return redirect()->route('website.account')->with('success', 'Course enrolled successfully');
            }
            
            // For Orders
            return redirect()->route('website.checkout.success', ['orderNumber' => $result['order_number'] ?? $result['order_id']]);
        }

        return redirect()->route('payment.error')->with('error', $result['message']);
    }

    /**
     * Handle payment webhook from MyFatoorah (server-to-server)
     */
    public function webhook(Request $request)
    {
        Log::info('Payment webhook received', $request->all());
        
        $result = $this->paymentService->handlePaymentCallback($request);

        return response()->json($result);
    }
}
