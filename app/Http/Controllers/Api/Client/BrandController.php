<?php

namespace App\Http\Controllers\Api\Client;

use App\Facades\Responder;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BrandResource;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * @var BrandService
     */

    /**
     * BrandController constructor.
     *
     * @param BrandService $brandService
     */
    public function __construct(protected BrandService $brandService)
    {
    }

    /**
     * Get all brands
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   

    /**
     * Get a brand with its products
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

        public function index(Request $request)
    {
        $onboarding = $request->query('onboarding');
        $brands = $this->brandService->getAllBrands($onboarding);
        return Responder::success(
            BrandResource::collection($brands)
        );
    }


}
