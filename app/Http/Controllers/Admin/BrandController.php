<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\brands\Store;
use App\Http\Requests\Admin\brands\Update;
use App\Models\Brand ;
use App\Traits\Report;


class BrandController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $brands = Brand::search(request()->searchArray)->paginate(30);
            $html = view('admin.brands.table' ,compact('brands'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.brands.index');
    }

    public function create()
    {
        return view('admin.brands.create');
    }


    public function store(Store $request)
    {
        Brand::create($request->validated());
        Report::addToLog('  اضافه العلامه التجاريه') ;
        return response()->json(['url' => route('admin.brands.index')]);
    }
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit' , ['brand' => $brand]);
    }

    public function update(Update $request, $id)
    {
        $brand = Brand::findOrFail($id)->update($request->validated());
        Report::addToLog('  تعديل العلامه التجاريه') ;
        return response()->json(['url' => route('admin.brands.index')]);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.show' , ['brand' => $brand]);
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id)->delete();
        Report::addToLog('  حذف العلامه التجاريه') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (Brand::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من العلامات التجاريه') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
