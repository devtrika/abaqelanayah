<?php

namespace App\Http\Controllers\Admin;

use App\Models\City ;
use App\Traits\Report;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\cities\Store;
use App\Models\Region;

class CityController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $cities = City::with('region')->search(request()->searchArray)->paginate(30);
            $html = view('admin.cities.table' ,compact('cities'))->render() ;
            return response()->json(['html' => $html]);
        }
        $regions = Region::get() ; 
        return view('admin.cities.index' , compact('regions'));
    }

    public function create()
    {
        $regions = Region::get() ; 
        return view('admin.cities.create' , compact('regions'));
    }

    public function store(Store $request)
    {
        $data = $request->validated();
        $region = \App\Models\Region::find($data['region_id']);
        if ($region) {
            $data['country_id'] = $region->country_id;
        }
        City::create($data);
        Report::addToLog('اضافه مدينة') ;
        return response()->json(['url' => route('admin.cities.index')]);
    }

    public function edit($id)
    {
        $regions = Region::get() ; 
        $city = City::findOrFail($id);
        return view('admin.cities.edit' , ['city' => $city , 'regions' => $regions]);
    }

    public function update(Store $request, $id)
    {
        $data = $request->validated();
        $region = \App\Models\Region::find($data['region_id']);
        if ($region) {
            $data['country_id'] = $region->country_id;
        }
        $city = City::findOrFail($id)->update($data);
        Report::addToLog('  تعديل مدينة') ;
        return response()->json(['url' => route('admin.cities.index')]);
    }

    
     public function show($id)
     {
         $city = City::findOrFail($id);
         $regions = Region::get() ; 
         return view('admin.cities.show' , ['city' => $city , 'regions' => $regions]);
     }
 
    public function destroy($id)
    {
        $city = City::findOrFail($id)->delete();
        Report::addToLog('  حذف مدينة') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (City::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من المدن') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
