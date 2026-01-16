<?php
namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateOrderRequest;
use App\Http\Resources\OrderRatingResource;
use App\Services\RatingService;
use App\Facades\Responder;

class RatingController extends Controller
{
    protected RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

public function store(RateOrderRequest $request)
{
    if ($this->ratingService->isOrderAlreadyRated($request->order_id)) {
        return response()->json([
            'status' => 402,
            'data' => null,
            'message' => __('apis.order_already_rated'),
        ], 402);
    }

    $this->ratingService->rateOrder($request->validated());

    return Responder::success(null, ['message' => __('apis.rating_submitted')]);
}


    public function myRatings()
    {
        $ratings = $this->ratingService->getUserRatings();

        return Responder::success(OrderRatingResource::collection($ratings));
    }
}
