<?php

namespace App\Http\Controllers\Api\Client;

use App\Facades\Responder;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryWithProductsResource;
use App\Http\Resources\Api\Settings\CategoryResource;
use App\Http\Resources\Api\SubCategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */

    /**
     * CategoryController constructor.
     *
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {
    }

    /**
     * Get all categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   

    /**
     * Get a category with its products
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

        public function index(Request $request)
    {
        $categories = $this->categoryService->getAllCategories();
        return Responder::success(
            CategoryResource::collection($categories)
        );
    }
public function show($id)
{
    $category = $this->categoryService->getCategoryWithProducts($id);

    if (!$category) {
        return Responder::error(__('apis.category_not_found'), [], 404);
    }

    return Responder::success(
        CategoryWithProductsResource::collection($category->children)
    );
}

}
