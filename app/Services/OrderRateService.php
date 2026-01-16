<?php

namespace App\Services;

use App\Models\OrderRating;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderRate;
use App\Enums\OrderStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRateService
{
    /**
     * Rate an order
     *
     * @param User $user
     * @param array $data
     * @return OrderRate
     * @throws \Exception
     */
    public function rateOrder(User $user, array $data): OrderRating
    {
        DB::beginTransaction();
    
        try {
            // Get the order
            $order = Order::findOrFail($data['order_id']);
    
            // Validate that the order belongs to the user
            $this->validateOrderOwnership($order, $user);
    
            // Validate that the order can be rated
            $this->validateOrderCanBeRated($order);
    
            // Check if order is already rated
            $this->validateOrderNotAlreadyRated($order);
    
            // Create the rating
            $rating = OrderRating::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'rating' => $data['rating'] ?? null,
                'comment' => $data['comment'] ?? null,
            ]);
    
            // Handle images
            if (!empty($data['images']) && is_array($data['images'])) {
                $rating->clearMediaCollection('order_rates');
                foreach ($data['images'] as $image) {
                    if ($image instanceof UploadedFile) {
                        $rating->addMedia($image)->toMediaCollection('order_rates');
                    }
                }
            }
    
            // Handle videos
            if (!empty($data['videos']) && is_array($data['videos'])) {
                foreach ($data['videos'] as $video) {
                    if ($video instanceof UploadedFile) {
                        $rating->addMedia($video)->toMediaCollection('order_rate_videos');
                    }
                }
            }
    
            DB::commit();
    
            return $rating->load('order');
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order rating failed', [
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
     * Validate that the order can be rated (must be delivered)
     *
     * @param Order $order
     * @throws \Exception
     */
    private function validateOrderCanBeRated(Order $order): void
    {
        if ($order->status !== OrderStatus::DELIVERED->value) {
            throw new \Exception(__('apis.order_must_be_delivered_to_rate'));
        }
    }

    /**
     * Validate that the order is not already rated
     *
     * @param Order $order
     * @throws \Exception
     */
    private function validateOrderNotAlreadyRated(Order $order): void
    {
        if ($order->rating) {
            throw new \Exception(__('apis.order_already_rated'));
        }
    }
}
