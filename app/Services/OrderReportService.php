<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderReportService
{
    /**
     * Report an order
     *
     * @param User $user
     * @param array $data
     * @return OrderReport
     * @throws \Exception
     */
    public function reportOrder(User $user, array $data): OrderReport
    {
        DB::beginTransaction();

        try {
            // Get the order
            $order = Order::findOrFail($data['order_id']);

            // Validate that the order belongs to the user
            $this->validateOrderOwnership($order, $user);

            // Check if order is already reported by this user
            $this->validateOrderNotAlreadyReported($order, $user);

            // Create the report
            $report = OrderReport::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'note' => $data['note'],
            ]);

            DB::commit();

            return $report->load(['order', 'user']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order reporting failed', [
                'error' => $e->getMessage(),
                'order_id' => $data['order_id'] ?? null,
                'user_id' => $user->id
            ]);
            throw $e;
        }
    }

   
    /**
     * Validate that the order belongs to the user
     *
     * @param Order $order
     * @param User $user
     * @throws \Exception
     */
    private function validateOrderOwnership(Order $order, User $user): void
    {
        if ($order->user_id !== $user->id) {
            throw new \Exception(__('apis.order_not_belongs_to_user'));
        }
    }

    /**
     * Validate that the order is not already reported by this user
     *
     * @param Order $order
     * @param User $user
     * @throws \Exception
     */
    private function validateOrderNotAlreadyReported(Order $order, User $user): void
    {
        $existingReport = OrderReport::where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReport) {
            throw new \Exception(__('apis.order_already_reported'));
        }
    }

    /**
     * Validate that the report belongs to the user
     *
     * @param OrderReport $report
     * @param User $user
     * @throws \Exception
     */
    private function validateReportOwnership(OrderReport $report, User $user): void
    {
        if ($report->user_id !== $user->id) {
            throw new \Exception(__('apis.report_not_belongs_to_user'));
        }
    }
}
