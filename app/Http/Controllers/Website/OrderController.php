<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use niklasravnsborg\LaravelPdf\Facades\Pdf as FacadesPdf;
use App\Models\Favorite;
use App\Models\Product;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // Base query: authenticated user's orders that are not refund orders and not refundable
        // Exclude orders where ALL items are fully refunded
        $baseQuery = Order::query()
            ->where('user_id', $user->id)
            ->whereHas('items', function ($query) {
                // Ensure order has at least one item that is NOT fully refunded
                $query->where('request_refund', false);
            })
            ->orderByDesc('created_at');

        // Status groupings for tabs
        $ongoingStatuses = ['pending', 'processing', 'new', 'confirmed', 'problem', 'request_cancel'];
        $completedStatuses = ['delivered'];
        $cancelledStatuses = ['cancelled'];

        // Determine active tab from query string (supports group aliases or raw statuses)
        $activeTab = 'ongoing';
        $statusParam = $request->string('status')->toString();
        if ($statusParam) {
            if (in_array($statusParam, ['ongoing', 'completed', 'cancelled'])) {
                $activeTab = $statusParam;
            } elseif (in_array($statusParam, array_merge($ongoingStatuses, $completedStatuses, $cancelledStatuses))) {
                $activeTab = in_array($statusParam, $ongoingStatuses) ? 'ongoing'
                    : (in_array($statusParam, $completedStatuses) ? 'completed' : 'cancelled');
            }
        }

        // Fetch grouped orders for the three tabs
        $ongoingOrders = (clone $baseQuery)->whereIn('status', $ongoingStatuses)->get();
        $completedOrders = (clone $baseQuery)->whereIn('status', $completedStatuses)->get();
        $cancelledOrders = (clone $baseQuery)->whereIn('status', $cancelledStatuses)->get();

        return view('website.pages.account.orders', compact('ongoingOrders', 'completedOrders', 'cancelledOrders', 'activeTab'));
    }

    public function show(Request $request, Order $order)
    {
        // if ($order->user_id !== $request->user()->id) {
        //     abort(404);
        // }

        $order->load([
            'items.item',
            'address.city',
            'address.district',
            'paymentMethod',
            'branch',
            'city',
            'giftCity',
            'giftDistrict',
            'cancelReason',
            'problem',
            'refundReason',
        ]);

        $refundReasons = \App\Models\RefundReason::all();

        return view('website.pages.account.order', compact('order', 'refundReasons'));
    }

    public function report(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('apis.unauthorized')], 403);
        }

        $data = $request->validate([
            'problem_id' => 'nullable|exists:problems,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (empty($data['problem_id']) && empty($data['notes'])) {
            return response()->json(['success' => false, 'message' => __('apis.problem_or_notes_required')], 422);
        }
        if (!empty($data['problem_id']) && !empty($data['notes'])) {
            return response()->json(['success' => false, 'message' => __('apis.problem_or_notes_conflict')], 422);
        }

        // If already in problem status, don't re-report
        if ($order->status === 'problem') {
            return response()->json(['success' => false, 'message' => __('apis.order_already_in_problem')], 400);
        }

        $order->update([
            'status' => 'problem',
            'problem_id' => $data['problem_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Flash success so shared toast shows after reload
        session()->flash('success', __('apis.problem_reported_successfully'));

        return response()->json([
            'success' => true,
            'message' => __('apis.problem_reported_successfully'),
            'new_status' => 'problem'
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('apis.unauthorized')], 403);
        }

        $data = $request->validate([
            'cancel_reason_id' => 'nullable|exists:cancel_reasons,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Require either a predefined reason or a free-text note, not both
        if (empty($data['cancel_reason_id']) && empty($data['notes'])) {
            return response()->json(['success' => false, 'message' => 'من فضلك اختر سبب الإلغاء أو أدخل سبباً نصياً'], 422);
        }
        if (!empty($data['cancel_reason_id']) && !empty($data['notes'])) {
            return response()->json(['success' => false, 'message' => 'يرجى اختيار سبب واحد فقط، إمّا سبب مُسبق أو سبب نصي'], 422);
        }

        // Check if order can be cancelled
        $allowedStatuses = ['processing', 'pending', 'new'];
        if (!in_array($order->status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => __('apis.order_cannot_be_cancelled')], 400);
        }
        if ($order->status === 'request_cancel') {
            return response()->json(['success' => false, 'message' => __('apis.order_already_requested_cancel')], 400);
        }

        $order->update([
            'status' => 'request_cancel',
            'cancel_reason_id' => $data['cancel_reason_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Flash success so shared toast shows after reload
        session()->flash('success', __('apis.cancellation_request_submitted_successfully'));

        return response()->json([
            'success' => true,
            'message' => __('apis.cancellation_request_submitted_successfully'),
            'new_status' => 'request_cancel'
        ]);
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        // Ensure the authenticated user owns the order
        if (!$request->user() || $order->user_id !== $request->user()->id) {
            abort(404);
        }

        // Eager load relations used in invoice and order view for full details
        $order->load([
            'user',
            'items.item',
            'address.city',
            'address.district',
            'paymentMethod',
            'branch',
            'city',
            'giftCity',
            'giftDistrict',
            'cancelReason',
            'problem',
        ]);

        // Prepare data for invoice view (aligns with API method structure)
        $invoiceData = [
            'order' => $order,
            'customer' => $order->user,
            'items' => $order->items,
            'totals' => [
                'products_total' => (float) $order->subtotal,
                'amount' => (float) $order->discount_amount,
                'delivery_fee' => (float) $order->delivery_fee,
                'wallet_deduction' => (float) $order->wallet_deduction,
                'loyalty_deduction' => (float) $order->loyalty_deduction,
                'final_total' => (float) $order->total,
            ],
            'address' => $order->address,
            'branch' => $order->branch,
            'payment_method' => $order->paymentMethod,
        ];

        // Website-only: return printable HTML view with auto-print script
        return view('invoices.website_order_invoice_print', [
            'order' => $order,
        ]);
    }

    public function favourits(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $favourites = $user->favourites()
            ->with(['product' => function ($q) {
                $q->with(['brand', 'category', 'parentCategory']);
            }])
            ->get();

        return view('website.pages.account.favourits', compact('favourites'));
    }

    public function addFavourite(Request $request)
    {
        $user = $request->user();
        $data = $request->validate(['product_id' => 'required|exists:products,id']);
        $pid = (int) $data['product_id'];
    
        $exists = Favorite::where('user_id', $user->id)->where('product_id', $pid)->exists();
        if (!$exists) {
            Favorite::create(['user_id' => $user->id, 'product_id' => $pid]);
        }
    
        return response()->json(['success' => true, 'favorited' => true, 'product_id' => $pid]);
    }
    
    public function removeFavourite(Request $request, $productId)
    {
        $user = $request->user();
        $pid = (int) $productId;
    
        Favorite::where('user_id', $user->id)->where('product_id', $pid)->delete();
    
        return response()->json(['success' => true, 'favorited' => false, 'product_id' => $pid]);
    }
    
    public function favouritesIds(Request $request)
    {
        $user = $request->user();
        $ids = Favorite::where('user_id', $user->id)->pluck('product_id');
        return response()->json(['success' => true, 'ids' => $ids]);
    }

    /**
     * Submit refund request from website (mirrors client API structure)
     */
    public function requestRefund(\App\Http\Requests\CreateRefundRequest $request)
    {
        try {
            $data = $request->validated();

            // Determine reason text (notes preferred)
            $refundReasonId = $data['refund_reason_id'] ?? null;
            $notes = $data['notes'] ?? null;
            $reasonText = null;
            if (!empty($notes)) {
                $reasonText = $notes;
            } elseif (!empty($refundReasonId)) {
                $refundReason = \App\Models\RefundReason::find($refundReasonId);
                $reasonText = $refundReason ? $refundReason->reason : null;
            }

            // Product IDs to refund
            $productIds = $data['items'];

            // Images (optional)
            $images = $request->hasFile('images') ? $request->file('images') : [];

            // Capture original status before invoking service
            $originalOrder = \App\Models\Order::findOrFail($data['order_id']);
            $originalStatus = $originalOrder->status;

            // Use RefundService (same as API) to mark items and set refundable flag
            $refundService = app(\App\Services\Order\RefundService::class);
            $order = $refundService->requestRefund(
                $data['order_id'],
                $productIds,
                $refundReasonId,
                $reasonText,
                $images,
                $notes
            );

            // WEBSITE-ONLY: Keep original order status and set separate refund_status
            $order->update([
                'status' => $originalStatus,
                'refund_status' => 'request_refund',
            ]);
            $order->refresh();

            // For website, return JSON so modal can reload the page
            return response()->json([
                'success' => true,
                'message' => __('apis.refund_request_submitted_successfully'),
                'redirect_url' => route('website.refunds.show', $order->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}