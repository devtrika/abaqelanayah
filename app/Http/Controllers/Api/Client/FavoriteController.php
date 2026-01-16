<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteRequest;
use App\Http\Resources\Api\ProductIndexResource;
use App\Services\FavoriteService;
use App\Facades\Responder;

class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index()
    {
		$data = $this->favoriteService->index();
		$products = $data->pluck('product')->filter();

		return Responder::success(ProductIndexResource::collection($products));
    }
    public function toggle(FavoriteRequest $request)
    {
        $result = $this->favoriteService->toggleFavorite($request->product_id);

        return Responder::success(null, ['message' => $result['message']]);
    }
}
