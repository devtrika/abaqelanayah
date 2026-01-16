<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\BrandService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService,
        protected BrandService $brandService
    ) {}

    /**
     * Display products by category with filters and pagination
     *
     * @param \Illuminate\Http\Request $request
     * @param int $categoryId
     * @return \Illuminate\View\View
     */
    public function category(Request $request, $slug)
    {
        $category = Category::with('children')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        // Build filters from request
        $filters = $request->only(['sort_by', 'min_price', 'max_price', 'brand_id']);
        // Support multi-select arrays as well
        if ($request->filled('brand_ids')) {
            $filters['brand_ids'] = (array) $request->input('brand_ids');
        }
        if ($request->filled('category_ids')) {
            $filters['category_ids'] = (array) $request->input('category_ids');
        }
        $filters['is_active'] = 1;

        // If this is a parent category, show all products under its children via parent_category_id
        if (is_null($category->parent_id)) {
            $filters['parent_category_id'] = $category->id;
        } else { // child category
            $filters['category_id'] = $category->id;
        }

        $perPage = (int) $request->input('per_page', 12);
        $products = $this->productService->listPaginated($filters, $perPage);

        // Sidebar data
        $allCategories = $this->categoryService->getAllCategories();
        $brands = $this->brandService->getAllBrands();

        // For AJAX requests, return the products list partial only
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->view('website.partials.category_products_list', compact('products'));
        }

        return view('website.pages.products_cateogry', compact('category', 'products', 'allCategories', 'brands', 'filters'));
    }

    /**
     * Display products with offers/discounts
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function offers(Request $request)
    {
        // Build filters from request
        $filters = $request->only(['sort_by', 'min_price', 'max_price' , 'latest_offer']);
        $filters['is_active'] = 1;
        $filters['latest_offer'] = true;

        $perPage = (int) $request->input('per_page', 12);
        $products = $this->productService->listPaginated($filters, $perPage);

        // Get all active categories for sidebar/filter
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->get();

        // For AJAX requests, return the products list partial only
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->view('website.partials.category_products_list', compact('products'));
        }

        return view('website.pages.offers', compact('products', 'categories', 'filters'));
    }

    /**
     * Display latest added products
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function latest(Request $request)
    {
        // Build filters from request
        $filters = $request->only(['sort_by', 'min_price', 'max_price']);
        $filters['is_active'] = 1;

        // Default sort by latest if not specified
        if (empty($filters['sort_by'])) {
            $filters['sort_by'] = 'latest';
        }

        $perPage = (int) $request->input('per_page', 12);
        $products = $this->productService->listPaginated($filters, $perPage);

        // Get all active categories for sidebar/filter
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->get();

        // Get all active brands for sidebar/filter
        $brands = $this->brandService->getAllBrands();

        // For AJAX requests, return the products list partial only
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->view('website.partials.category_products_list', compact('products'));
        }

        return view('website.pages.latest_products', compact('products', 'categories', 'brands', 'filters'));
    }

    /**
     * Display products filtered by brand
     *
     * @param \Illuminate\Http\Request $request
     * @param int $brandId
     * @return \Illuminate\View\View
     */
    public function brand(Request $request, $brandId)
    {
        $brand = Brand::where('id', $brandId)
            ->where('is_active', 1)
            ->firstOrFail();

        // Build filters from request
        $filters = $request->only(['sort_by']);
        $filters['brand_id'] = $brandId;
        $filters['is_active'] = 1;

        $perPage = (int) $request->input('per_page', 12);
        $products = $this->productService->listPaginated($filters, $perPage);

        // For AJAX requests, return the products list partial only
        if ($request->ajax() || $request->boolean('ajax')) {
            return response()->view('website.partials.category_products_list', compact('products'));
        }

        return view('website.pages.products_brand', compact('brand', 'products', 'filters'));
    }

    /**
     * Display single product details
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $product = Product::with(['category.parent', 'brand', 'options'])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        // Get related products from the same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', 1)
            ->limit(8)
            ->get();

        return view('website.pages.product', compact('product', 'relatedProducts'));
    }

    /**
     * Live search for products (AJAX)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $products = Product::where('is_active', 1)
            ->where(function($q) use ($query) {
                $q->where('name->ar', 'like', '%' . $query . '%')
                  ->orWhere('name->en', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'price' => $product->final_price,
                    'url' => route('website.product.show', $product->slug)
                ];
            });

        return response()->json(['results' => $products]);
    }
}

