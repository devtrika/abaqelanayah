<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreRateRequest;
use App\Http\Resources\Api\Client\RateResource;
use App\Services\RateService;
use App\Facades\Responder;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class RateController extends Controller
{
    use ResponseTrait;

    /**
     * @var RateService
     */
    protected $rateService;

    /**
     * RateController constructor.
     *
     * @param RateService $rateService
     */
    public function __construct(RateService $rateService)
    {
        $this->rateService = $rateService;
    }

    /**
     * Store a new rating for any rateable item
     *
     * @param StoreRateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRateRequest $request)
    {
        try {
            $data = $request->validated();

            $rating = $this->rateService->store( $data);

            return Responder::success(
                new RateResource($rating),
                ['message' => __('apis.item_rated_successfully')]
            );
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Get ratings for a specific item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $rateableType = $request->input('rateable_type');
            $rateableId = $request->input('rateable_id');
            $perPage = $request->input('per_page', 10);

            if ($rateableType && $rateableId) {
                // Get ratings for specific item
                $modelClass = match($rateableType) {
                    'provider' => 'App\\Models\\Provider',
                    'product' => 'App\\Models\\Product',
                    'service' => 'App\\Models\\Service',
                    default => null
                };

                if (!$modelClass) {
                    return Responder::error(__('apis.invalid_rateable_type'), [], 400);
                }

                $rateable = $modelClass::findOrFail($rateableId);
                $ratings = $this->rateService->getRatingsForItem($rateable, $perPage);
            } else {
                // Get all ratings by type
                $type = $request->input('type', 'provider');
                $modelClass = match($type) {
                    'provider' => 'App\\Models\\Provider',
                    'product' => 'App\\Models\\Product',
                    'service' => 'App\\Models\\Service',
                    default => 'App\\Models\\Provider'
                };

                $ratings = $this->rateService->getRatingsByType($modelClass, $perPage);
            }

            return Responder::paginated(
                RateResource::collection($ratings)
            );
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Get a specific rating
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $rating = $this->rateService->getRatingById($id);

            if (!$rating) {
                return Responder::error(__('apis.rating_not_found'), [], 404);
            }

            return Responder::success(new RateResource($rating));
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Get rating statistics for a specific item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        try {
            $rateableType = $request->input('rateable_type');
            $rateableId = $request->input('rateable_id');

            if (!$rateableType || !$rateableId) {
                return Responder::error(__('apis.rateable_type_and_id_required'), [], 400);
            }

            $modelClass = match($rateableType) {
                'provider' => 'App\\Models\\Provider',
                'product' => 'App\\Models\\Product',
                'service' => 'App\\Models\\Service',
                default => null
            };

            if (!$modelClass) {
                return Responder::error(__('apis.invalid_rateable_type'), [], 400);
            }

            $rateable = $modelClass::findOrFail($rateableId);
            $stats = $this->rateService->getRatingStats($rateable);

            return Responder::success($stats);
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 500);
        }
    }
}
