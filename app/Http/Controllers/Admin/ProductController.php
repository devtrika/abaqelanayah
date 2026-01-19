<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Traits\Report;
use App\Models\Product;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Models\ProductOption;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\products\Store;
use App\Http\Requests\Admin\products\Update;


class ProductController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $products = Product::with(['category', 'options'])
                ->search(request()->searchArray)
                ->paginate(30);

            $html = view('admin.products.table', compact('products'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.products.index');
    }
    public function create()
    {
        $categories = Category::where('is_active', 1)->get();
        $brands  = Brand::where('is_active',1)->get();
        return view('admin.products.create', compact('categories','brands'));
    }


    public function store(Store $request)
    {
        $product = Product::create($request->validated());

       
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $product->addMedia($file)->toMediaCollection('product-images');
                    }
                }
            }
        // Log success
        Report::addToLog('اضافه منتج');

        return response()->json(['url' => route('admin.products.index')]);
    }

    public function edit($id)
    {
        $product = Product::with('options')->findOrFail($id);

        // Load ALL categories and brands (both active and inactive)
        // This allows editing products with inactive categories/brands
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.edit', compact('product', 'categories','brands'));
    }
    

     public function update(Update $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        // Get current admin
        $admin = auth()->guard('admin')->user();

        // For other admin roles: proceed with normal product update
        $product->update($request->validated());

        // Handle image deletion first
        if ($request->has('deleted_media') && is_array($request->deleted_media)) {
            foreach ($request->deleted_media as $mediaId) {
                $media = $product->getMedia('product-images')->where('id', $mediaId)->first();
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $product->addMedia($file)->toMediaCollection('product-images');
                }
            }
        }

        Report::addToLog('تعديل منتج');

        return response()->json(['url' => route('admin.products.index')]);
    }


    

    public function show($id)
    {
        $product = Product::with(['category', 'options'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product is related to any orders
        if ($product->orderItems()->exists()) {
            return response()->json([
                'error' => true,
                'message' => 'لا يمكن حذف هذا المنتج لأنه مرتبط بطلبات موجودة'
            ], 422);
        }
        
        $product->delete();
        Report::addToLog('حذف منتج');
        return response()->json(['id' => $id]);
    }

    /**
     * Delete multiple products
     */
    public function deleteAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        $ids = [];
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        
        // Check if any products are related to orders
        $productsWithOrders = Product::whereIntegerInRaw('id', $ids)
            ->whereHas('orderItems')
            ->pluck('name')
            ->toArray();
            
        if (!empty($productsWithOrders)) {
            $productNames = implode('، ', $productsWithOrders);
            return response()->json([
                'error' => true,
                'message' => 'لا يمكن حذف المنتجات التالية لأنها مرتبطة بطلبات موجودة: ' . $productNames
            ], 422);
        }
        
        if (Product::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من منتجات') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    /**
     * Toggle product status
     */
    public function toggleStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        Report::addToLog($status . ' منتج');

        return response()->json([
            'status' => $product->is_active,
            'message' => 'تم تحديث حالة المنتج بنجاح'
        ]);
    }

public function deleteImage($mediaId)
{
    $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mediaId);
    if ($media) {
        $media->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'الصورة غير موجودة']);
}
}
