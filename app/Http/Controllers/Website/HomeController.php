<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\IntroSlider;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get sliders
        $sliders = Image::where('is_active', 1)->get();

        // Get active categories (only parent categories)
        $categories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->get();

        // Get products with offers/discounts
        $specialOffers = Product::where('is_active', 1)
            ->where('discount_percentage', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get latest added products
        $latestProducts = Product::where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get active brands
        $brands = Brand::where('is_active', 1)->get();

        return view('website.pages.home', compact('sliders', 'categories', 'specialOffers', 'latestProducts', 'brands'));
    }
}

