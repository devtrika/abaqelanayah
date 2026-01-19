<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus as OrderStatusModel;
use App\Models\ProviderSubOrder;
use App\Traits\Report;
use App\Services\Order\OrderStatusService;
use App\Services\Order\OrderNotificationService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $statusService;
    protected $notificationService;

    public function __construct(
        OrderStatusService $statusService,
        OrderNotificationService $notificationService
    ) {
        $this->statusService = $statusService;
        $this->notificationService = $notificationService;
    }

    /**
     * Assign delivery user and change status to delivering
     */
    public function assignDelivery(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $deliveryUserId = $request->input('delivery_user_id');
            if (!$deliveryUserId) {
                return response()->json(['success' => false, 'message' => 'يرجى اختيار مندوب التوصيل'], 400);
            }
            // Check if user exists and is delivery type
            $deliveryUser = \App\Models\User::where('id', $deliveryUserId)->where('type', 'delivery')->first();
            if (!$deliveryUser) {
                return response()->json(['success' => false, 'message' => 'مندوب التوصيل غير موجود أو غير صحيح'], 400);
            }
            // Assign delivery user and change status to new
            // Save the assigned delivery user into the delivery_id field (not user_id)
            $order->delivery_id = $deliveryUserId;
            $order->status = 'new';
            $order->save();
            // Add status history
            OrderStatusModel::create([
                'order_id' => $order->id,
                'status' => 'new',
                'map_desc' => 'تم تعيين مندوب التوصيل وتغيير الحالة إلى مسئول التوصيل'
            ]);

            // Send notifications
            // 1. Notify delivery person of assignment
            $this->notificationService->notifyDeliveryOfAssignment($order->fresh('delivery'));

            // 2. Notify client that delivery person has been assigned
            $this->notificationService->notifyClientOfDeliveryAssignment($order->fresh('user'));

            return response()->json([
                'success' => true,
                'message' => 'تم تعيين مندوب التوصيل بنجاح',
                'delivery_id' => $order->delivery_id,
                'status' => $order->status,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تعيين مندوب التوصيل: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Display the orders dashboard with tabs
     */
    public function dashboard()
    {
        return view('admin.orders.dashboard');
    }

    public function index($id = null)
    {
        if (request()->ajax()) {
            $admin = auth()->guard('admin')->user();

            $query = Order::with(['user', 'address', 'paymentMethod']);

            $orders = $query
                ->search(request()->searchArray)
                ->paginate(30);

            $html = view('admin.orders.table', compact('orders'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.orders.index');
    }

    /**
     * Display orders filtered by status
     */
    public function indexByStatus($status)
    {
        // Validate status
        $allowedStatuses = ['pending', 'new', 'confirmed', 'delivered', 'cancelled', 'refunded'];
        if (!in_array($status, $allowedStatuses)) {
            abort(404);
        }

        if (request()->ajax()) {
            $admin = auth()->guard('admin')->user();

            $query = Order::with(['user', 'address', 'paymentMethod'])
                ->where('status', $status);

            $orders = $query
                ->search(request()->searchArray)
                ->paginate(30);

            $html = view('admin.orders.table', compact('orders'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.orders.index', compact('status'));
    }

    /**
     * Get order counts by status for dashboard
     */
    public function getCounts()
    {
        $admin = auth()->guard('admin')->user();

        $query = Order::query();

        return response()->json([
            'all_orders' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'new' => (clone $query)->where('status', 'new')->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'refunded' => (clone $query)->where('status', 'refunded')->count(),
            'request_refund' => (clone $query)->where('status', 'request_refund')->count(),
        ]);
    }

    public function create()
    {
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        $order = Order::create($request->all());

        // Log success
        Report::addToLog('اضافة منتج');

        return response()->json(['url' => route('admin.orders.index')]);
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', ['order' => $order]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());

        // Log success
        Report::addToLog('تعديل منتج');

        return response()->json(['url' => route('admin.orders.index')]);
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.item',
            'coupon',
            'rate',
            'rates',
        ])->findOrFail($id);

        // Get payment method details
    $paymentMethod = \App\Models\PaymentMethod::find($order->payment_method_id);
    
    // Get all delivery users
    $deliveryUsers = \App\Models\User::where('type', 'delivery')->where('accept_orders', true)->get();

    // Get cost_details from resource and expose to admin view
        $orderResource = new \App\Http\Resources\Api\Client\OrderDetailsResource($order);
        // The API resource provides 'cost_details' (not 'cost_details_arabic')
        $order->cost_details_arabic = $orderResource->toArray(request())['cost_details'] ?? [];

        // Fallback: if resource didn't provide cost details (or returned empty), compute from order fields
        if (empty($order->cost_details_arabic)) {
            $products_total_without_vat = (float) ($order->subtotal ?? 0);
            $discount_amount = (float) ($order->discount_amount ?? 0);
            $delivery_fee = (float) ($order->delivery_fee ?? 0);
            $vat_amount = (float) ($order->vat_amount ?? 0);
            $wallet_deduction = (float) ($order->wallet_deduction ?? 0);
            $total_with_vat = ($products_total_without_vat - $discount_amount + $delivery_fee + $vat_amount);
            $order->cost_details_arabic = [
                'products_total_without_vat' => $products_total_without_vat,
                'discount_code' => null,
                'products_total_after_discount' => ($products_total_without_vat - $discount_amount),
                'delivery_fee' => $delivery_fee,
                'total_without_vat' => ($products_total_without_vat - $discount_amount + $delivery_fee),
                'vat_percent' => '15%',
                'vat_amount' => $vat_amount,
                'total_with_vat' => $total_with_vat,
                'wallet_deduction' => $wallet_deduction,
                'final_total' => (float) ($order->total ?? 0),
            ];
        }

        return view('admin.orders.show', compact('order', 'paymentMethod','deliveryUsers'));
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        Report::addToLog('حذف منتج');
        return response()->json(['id' => $id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);

        $ids = collect($requestIds)->pluck('id')->toArray();

        if (Order::whereIntegerInRaw('id', $ids)->get()->each->delete()) {
            Report::addToLog('حذف العديد من المنتجات');
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    /**
     * Update order status
     */

    /**
     * Change order status with comprehensive updates
     */
    public function changeOrderStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $newStatus = $request->input('status');
            $notes = $request->input('notes', '');

            // Validate status - allow any defined status from the enum
            $allowedStatuses = \App\Enums\OrderStatus::values();

            if (!in_array($newStatus, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status provided'
                ], 400);
            }

            // Get current admin
            $admin = auth()->guard('admin')->user();

            // Start transaction
            DB::beginTransaction();

            try {
                // Handle cancellation separately
                if ($newStatus === 'cancelled') {
                    DB::rollback(); // Rollback this transaction
                    $this->statusService->cancelOrder($order, $notes ?: 'Cancelled by admin', $admin);
                } else {
                    // Update order status using service
                    $this->statusService->updateStatus($order, $newStatus, [
                        'user_type' => 'admin',
                        'notify' => true
                    ]);
                }

                // Create status history for main order
                OrderStatusModel::create([
                    'order_id' => $order->id,
                    'status' => $newStatus,
                    'map_desc' => $notes ?: "Status changed to {$newStatus} by admin"
                ]);

                DB::commit();

                // Log the action
                Report::addToLog("تغيير حالة الطلب رقم {$order->order_number} إلى {$newStatus}");

                return response()->json([
                    'success' => true,
                    'message' => __('admin.order_status_updated_successfully') . " " . __('admin.' . $newStatus),
                    'new_status' => $newStatus
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send order status notification to user in both Arabic and English
     * (Kept for backward compatibility, but now uses OrderNotificationService)
     */
    private function sendOrderStatusNotificationToUser($order, $newStatus)
    {
        $this->notificationService->notifyUserOfStatusChange($order, $newStatus);
    }
}
