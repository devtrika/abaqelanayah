<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\regions\Store;
use App\Http\Requests\Admin\regions\Update;
use App\Models\Country;
use App\Models\Region ;
use App\Traits\Report;


class RegionController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $regions = Region::search(request()->searchArray)->paginate(30);
            $html = view('admin.regions.table' ,compact('regions'))->render() ;
            return response()->json(['html' => $html]);
        }
        $countries = Country::get();
        return view('admin.regions.index' ,compact('countries'));
    }

    public function create()
    {
        $countries = Country::get();
        return view('admin.regions.create' ,compact('countries'));
    }


    public function store(Store $request)
    {
        Region::create($request->validated());
        Report::addToLog('  اضافه مناطق') ;
        return response()->json(['url' => route('admin.regions.index')]);
    }
    public function edit($id)
    {
        $region = Region::findOrFail($id);
        $countries = Country::get();
        $allCities = \App\Models\City::all();
        return view('admin.regions.edit' , ['region' => $region ,'countries' => $countries, 'allCities' => $allCities]);
    }

    public function update(Update $request, $id)
    {
        $region = Region::findOrFail($id);
        $region->update($request->validated());
        if ($request->has('cities')) {
            \App\Models\City::whereIn('id', $request->cities)->update(['region_id' => $region->id]);
            // Optionally, unassign cities not in the list
            \App\Models\City::where('region_id', $region->id)->whereNotIn('id', $request->cities)->update(['region_id' => null]);
        }
        Report::addToLog('  تعديل مناطق') ;
        return response()->json(['url' => route('admin.regions.index')]);
    }

    public function show($id)
    {
        $countries = Country::get();
        $region = Region::findOrFail($id);
        $cities = $region->cities()->get();
        return view('admin.regions.show' , ['region' => $region ,'countries' => $countries, 'cities' => $cities]);
    }
    public function destroy($id)
    {
        $region = Region::findOrFail($id)->delete();
        Report::addToLog('  حذف مناطق') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (Region::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من منظقة') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
