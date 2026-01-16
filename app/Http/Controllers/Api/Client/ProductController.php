<?php
namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductIndexResource;
use App\Http\Resources\Api\ProductResource;
use App\Services\ProductService;
use App\Services\Responder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service)
    {}

    public function index(Request $request)
    {
        $filters              = $request->only(['sort_by', 'name', 'category_id', 'parent_category_id', 'min_price', 'max_price','brand_id']);
        $filters['is_active'] = 1;
        $products             = ProductIndexResource::collection($this->service->list($filters));
        return Responder::success($products, __('apis.success'));
    }

    public function show($id)
    {
        $product = $this->service->get($id);
        if (! $product || ! $product->is_active) {
            return Responder::error(null, __('apis.not_found'), 404);
        }
        return Responder::success(new ProductResource($product), __('apis.success'));
    }

    public function latestOffers()
    {
        $products = ProductIndexResource::collection(
            $this->service->latestOffers()->where('is_active', 1)
        );
        return Responder::success($products, __('apis.success'));
    }

}
