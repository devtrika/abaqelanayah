<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\GiftResource;
use App\Services\GiftService;
use App\Facades\Responder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftController extends Controller
{
    protected $giftService;

    public function __construct(GiftService $giftService)
    {
        $this->giftService = $giftService;
    }

    /**
     * Get active gifts for the current month
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return Responder::error(__('apis.unauthorized'), [], 401);
            }

            $gifts = $this->giftService->getGiftsWithProgress($userId);

            if ($gifts->isEmpty()) {
                return Responder::success([], ['message' => __('apis.no_gifts_available')]);
            }

            return Responder::success(
                GiftResource::collection($gifts),
                ['message' => __('apis.gifts_retrieved_successfully')]
            );

        } catch (\Exception $e) {
            return Responder::error(
                __('apis.something_went_wrong'),
                [$e->getMessage()],
                500
            );
        }
    }

    /**
     * Get available gifts (achieved by user)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function available(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return Responder::error(__('apis.unauthorized'), [], 401);
            }

            $gifts = $this->giftService->getAvailableGifts($userId);

            return Responder::success(
                GiftResource::collection($gifts),
                ['message' => __('apis.available_gifts_retrieved_successfully')]
            );

        } catch (\Exception $e) {
            return Responder::error(
                __('apis.something_went_wrong'),
                [$e->getMessage()],
                500
            );
        }
    }

    /**
     * Get pending gifts (not yet achieved by user)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return Responder::error(__('apis.unauthorized'), [], 401);
            }

            $gifts = $this->giftService->getPendingGifts($userId);

            return Responder::success(
                GiftResource::collection($gifts),
                ['message' => __('apis.pending_gifts_retrieved_successfully')]
            );

        } catch (\Exception $e) {
            return Responder::error(
                __('apis.something_went_wrong'),
                [$e->getMessage()],
                500
            );
        }
    }

    /**
     * Get user's current month order statistics
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return Responder::error(__('apis.unauthorized'), [], 401);
            }

            $orderCount = $this->giftService->getUserOrderCountForCurrentMonth($userId);
            $gifts = $this->giftService->getActiveGiftsForCurrentMonth();
            $availableGifts = $this->giftService->getAvailableGifts($userId);
            $pendingGifts = $this->giftService->getPendingGifts($userId);

            $stats = [
                'current_month_orders' => $orderCount,
                'total_gifts' => $gifts->count(),
                'available_gifts' => $availableGifts->count(),
                'pending_gifts' => $pendingGifts->count(),
                'month' => now()->format('F Y'),
            ];

            return Responder::success(
                $stats,
                ['message' => __('apis.stats_retrieved_successfully')]
            );

        } catch (\Exception $e) {
            return Responder::error(
                __('apis.something_went_wrong'),
                [$e->getMessage()],
                500
            );
        }
    }
}
