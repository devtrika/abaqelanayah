<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class RefundOrderController extends Controller
{
    /**
     * List all refundable orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch user's refundable orders with necessary relationships
        $refundOrders = Order::with([
                // Only items that have refund requested; include polymorphic item
                'items' => function ($q) {
                    $q->where('request_refund', true)->with(['item', 'product']);
                },
                'address',
                'paymentMethod',
            ])
            ->where('user_id', $user->id)
            ->where('refundable', true)
            ->orderByDesc('created_at')
            ->get();

        // Render existing refunds archive view (kept unchanged)
        return view('website.pages.account.refunds', compact('refundOrders'));
    }

    /**
     * Show a single refundable order details for the authenticated user.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Find the refundable order belonging to the user and eager load relations
        $order = Order::where('refundable', true)
            ->where('user_id', $user->id)
            ->with([
                'items' => function ($q) {
                    // Only include items requested for refund
                    $q->where('request_refund', true)->with(['item', 'product']);
                },
                'address.city',
                'address.district',
                'paymentMethod',
                'refundReason',
                'branch',
                'city',
            ])
            ->findOrFail($id);

        // Render existing refund order view (kept unchanged)
        return view('website.pages.account.refund_order', compact('order'));
    }
}