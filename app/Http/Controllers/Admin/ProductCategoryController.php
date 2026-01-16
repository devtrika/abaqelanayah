<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\productcategories\Store;
use App\Http\Requests\Admin\productcategories\Update;
use App\Models\ProductCategory ;
use App\Traits\Report;


class ProductCategoryController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $productcategories = ProductCategory::search(request()->searchArray)->paginate(30);
            $html = view('admin.productcategories.table' ,compact('productcategories'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.productcategories.index');
    }

    public function create()
    {
        return view('admin.productcategories.create');
    }


    public function store(Store $request)
    {
        $productCategory = ProductCategory::create($request->validated());

        // Handle image upload using Spatie Media Library
        if ($request->hasFile('image')) {
            $productCategory->addMediaFromRequest('image')
                ->usingName('product_category_image')
                ->usingFileName(time() . '_product_category_' . $productCategory->id . '.' . $request->file('image')->getClientOriginalExtension())
                ->toMediaCollection('product-categories');
        }

        Report::addToLog('  اضافه تصنيفات-المنتجات') ;
        return response()->json(['url' => route('admin.product-categories.index')]);
    }
    public function edit($id)
    {
        $productcategory = ProductCategory::findOrFail($id);
        return view('admin.productcategories.edit' , ['productcategory' => $productcategory]);
    }

    public function update(Update $request, $id)
    {
        $productCategory = ProductCategory::findOrFail($id);
        $productCategory->update($request->validated());

        // Handle image upload using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the collection
            $productCategory->clearMediaCollection('product-categories');

            // Add new media
            $productCategory->addMediaFromRequest('image')
                ->usingName('product_category_image')
                ->usingFileName(time() . '_product_category_' . $productCategory->id . '.' . $request->file('image')->getClientOriginalExtension())
                ->toMediaCollection('product-categories');
        }

        Report::addToLog('  تعديل تصنيفات-المنتجات') ;
        return response()->json(['url' => route('admin.product-categories.index')]);
    }

    public function show($id)
    {
        $productcategory = ProductCategory::findOrFail($id);
        return view('admin.productcategories.show' , ['productcategory' => $productcategory]);
    }
    public function destroy($id)
    {
        $productcategory = ProductCategory::findOrFail($id)->delete();
        Report::addToLog('  حذف تصنيفات-المنتجات') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);

        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (ProductCategory::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من تصنيف-منتج') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
