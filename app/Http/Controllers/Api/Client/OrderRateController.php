<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\RateOrderRequest;
use App\Http\Resources\Api\Order\OrderRateResource;
use App\Services\OrderRateService;
use App\Services\Responder;
use App\Traits\ResponseTrait;

class OrderRateController extends Controller
{
    use ResponseTrait;

    /**
     * @var OrderRateService
     */
    protected $orderRateService;

    /**
     * OrderRateController constructor.
     *
     * @param OrderRateService $orderRateService
     */
    public function __construct(OrderRateService $orderRateService)
    {
        $this->orderRateService = $orderRateService;
    }

    /**
     * Rate an order
     *
     * @param RateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RateOrderRequest $request)
    {
        try {
            $user = auth()->user();
            $rating = $this->orderRateService->rateOrder($user, $request->validated());
            // Send notification to all admins about order rating

            $this->sendOrderRatedNotificationToAdmins($rating);

            return Responder::success(
                null ,
                ['message' => __('apis.order_rated_successfully')]
            );
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    protected function sendOrderRatedNotificationToAdmins($rating)
    {
        $message = 'تم تقييم الطلب رقم #' . $rating->id;
        $admins = \App\Models\Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NotifyAdmin([
                'title' => [
                    'ar' => 'تقييم طلب',
                    'en' => 'Order Rated'
                ],
                'body' => [
                    'ar' => $message,
                    'en' => $message
                ],
                'type' => 'order_rating',
                'orderrate_id' => $rating->id

            ]));
        }
    }
}
