<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\User;
use App\Services\Order\RefundService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RefundOrderController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Display a listing of refund requests
     */
    public function index()
    {
        if (request()->ajax()) {
            $admin = auth()->guard('admin')->user();

            // Query orders with refundable flag (tracks refund lifecycle)
            $query = Order::with([
                'user',
                'items' => function($q) {
                    // Only load items requested for refund
                    $q->where('request_refund', true);
                },
                'items.product',
                'refundReason',
                'delivery'
            ])
            ->whereIn('refund_status', ['request_refund', 'request_rejected', 'new', 'refunded']);

            // If branch manager (role_id = 2), limit refund orders to their branch(es)
            if ($admin && (int) $admin->role_id === 2) {
                $branchIds = \App\Models\BranchManager::where('manager_id', $admin->id)->pluck('branch_id');

                if ($branchIds && $branchIds->count() > 0) {
                    $query->whereIn('branch_id', $branchIds);
                } else {
                    // No assigned branches => show no refund orders
                    $query->whereRaw('1 = 0');
                }
            }

            $refundOrders = $query
                ->orderBy('created_at', 'desc')
                ->paginate(30);

            $html = view('admin.refund_orders.table', compact('refundOrders'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.refund_orders.index');
    }

    /**
     * Display the specified refund request
     */
    public function show($id)
    {
        // Find order with refundable flag
        $order = Order::with([
                'user',
                'items' => function($query) {
                    // Only load items that were requested for refund
                    $query->where('request_refund', true);
                },
                'items.product',
                'coupon',
                'refundReason',
                'delivery'
            ])->findOrFail($id);

        // Get delivery persons for assignment
        $deliveryPersons = User::where('type', 'delivery')->get();

        // Get cost_details from resource
        $orderResource = new \App\Http\Resources\Api\Client\OrderDetailsResource($order);
        $costDetails = $orderResource->toArray(request())['cost_details'] ?? [];

        // Convert to label-value format for display
        $order->cost_details_arabic = [
            ['label' => __('admin.products_total_without_vat'), 'value' => number_format($costDetails['products_total_without_vat'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.products_total_after_discount'), 'value' => number_format($costDetails['products_total_after_discount'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.delivery_fee'), 'value' => number_format($costDetails['delivery_fee'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.total_without_vat'), 'value' => number_format($costDetails['total_without_vat'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.vat_percent'), 'value' => $costDetails['vat_percent'] ?? '15%'],
            ['label' => __('admin.vat_amount'), 'value' => number_format($costDetails['vat_amount'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.total_with_vat'), 'value' => number_format($costDetails['total_with_vat'] ?? 0, 2) . ' ' . __('admin.sar')],
            ['label' => __('admin.wallet_deduction'), 'value' => number_format($costDetails['wallet_deduction'] ?? 0, 2) . ' ' . __('admin.sar')],
        ];

        // Add discount if exists
        if (!empty($costDetails['discount_code'])) {
            array_splice($order->cost_details_arabic, 1, 0, [
                ['label' => $costDetails['discount_code']['label'] ?? __('admin.discount_code'), 'value' => $costDetails['discount_code']['code'] . ' (' . number_format($costDetails['discount_code']['amount'] ?? 0, 2) . ' ' . __('admin.sar') . ')']
            ]);
        }

        // For backward compatibility with views
        $refundOrder = $order;

        return view('admin.refund_orders.show', compact('refundOrder', 'order', 'deliveryPersons'));
    }

    /**
     * Accept a refund request
     */
    public function accept(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'nullable|numeric|min:0',
            'delivery_id' => 'required|exists:users,id',
            'items' => 'nullable|array',
            'items.*' => 'exists:order_items,id',
        ]);

        try {
            $order = Order::where('refundable', true)
                ->with([
                    'items' => function($q) {
                        // Load items with request_refund flag
                        $q->where('request_refund', true);
                    },
                    'items.product', 
                    'user'
                ])->findOrFail($id);

            // Check if order is in refund request status (using refund_status)
            if ($order->refund_status !== 'request_refund') {

                return response()->json([
                    'success' => false,
                    'message' => __('admin.order_not_in_refund_request_status')
                ], 400);
            }

            // Get refund amount (use from request or from order)
            $refundAmount = $request->refund_amount ?? $order->refund_amount;
            $deliveryId = $request->delivery_id;

            // Get items to refund (only items with request_refund = true)
            $items = [];
            if ($request->has('items') && !empty($request->items)) {
                // Admin selected specific items
                foreach ($request->items as $orderItemId) {
                    $orderItem = $order->items->firstWhere('id', $orderItemId);
                    if ($orderItem && $orderItem->request_refund) {
                        $availableQty = $orderItem->quantity - $orderItem->refund_quantity;
                        if ($availableQty > 0) {
                            $items[$orderItemId] = $availableQty;
                        }
                    }
                }
            } else {
                // Refund all items that have request_refund flag
                foreach ($order->items as $orderItem) {
                    if ($orderItem->request_refund) {
                        $availableQty = $orderItem->quantity - $orderItem->refund_quantity;
                        if ($availableQty > 0) {
                            $items[$orderItem->id] = $availableQty;
                        }
                    }
                }
            }

            // Preserve original status before service updates
            $originalStatus = $order->status;

            // Use RefundService to approve refund (updates refund flags and data)
            $order = $this->refundService->approveRefund(
                $id,
                $items,
                $refundAmount,
                $deliveryId
            );

            // ADMIN-ONLY: Keep original order status and set separate refund_status lifecycle
            $order->update([
                'status' => $originalStatus,
                'refund_status' => 'new',
            ]);
            $order->refresh();

            return response()->json([
                'success' => true,
                'message' => __('admin.refund_accepted_successfully'),
                'refund_number' => $order->refund_number,
                'redirect_url' => route('admin.refund_orders.show', ['id' => $order->id])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to accept refund', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuse a refund request
     */
    public function refuse($id)
    {
        try {
            $order = Order::where('refundable', true)->findOrFail($id);


            // Log refund state for debugging
            Log::info('Refuse refund requested', [
                'order_id' => $id,
                'status' => $order->status,
                'refund_status' => $order->refund_status,
            ]);

            // Check if order is in refund request status (using refund_status)
            if ($order->refund_status !== 'request_refund') {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.order_not_in_refund_request_status')
                ], 400);
            }

            // Preserve original status before service updates
            $originalStatus = $order->status;

            // Use RefundService to reject refund
            $order = $this->refundService->rejectRefund($id);

            // ADMIN-ONLY: Keep original order status and set refund_status to request_rejected
            $order->update([
                'status' => $originalStatus,
                'refund_status' => 'request_rejected',
            ]);
            $order->refresh();

            return response()->json([
                'success' => true,
                'message' => __('admin.refund_refused_successfully'),
                'redirect_url' => route('admin.refund_orders.index')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to refuse refund', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            $message = __('admin.failed_to_refuse_refund');
            // Append exception message in debug mode to help trace the issue
            if (config('app.debug')) {
                $message .= ' - ' . $e->getMessage();
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }
    }
}
