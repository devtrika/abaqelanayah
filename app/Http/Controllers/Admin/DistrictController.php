<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistrictController extends Controller
{


 public function index($id = null)
    {
        if (request()->ajax()) {

            $cities = District::search(request()->searchArray)->paginate(30);
           $html = view('admin.districts.table', ['districts' => $cities])->render();
            return response()->json(['html' => $html]);
        }
    $cities = \App\Models\City::all();
    $districts=District::all();
    return view('admin.districts.index', compact('cities', 'districts'));
    }

    public function create()
    {
        $cities = City::all();
        return view('admin.districts.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'name.ar' => 'required|string|max:191',
            'name.en' => 'required|string|max:191',
            'status' => 'required|boolean',
            'city_id' => 'required|exists:cities,id',
        ]);
        $data = $request->only(['name', 'status', 'city_id']);
        District::create($data);
        return response()->json(['url' => route('admin.districts.index')]);

    }

    public function edit($id)
    {
        $district = District::findOrFail($id);
        $cities = City::all();
        return view('admin.districts.edit', compact('district', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|array',
            'name.ar' => 'required|string|max:191',
            'name.en' => 'required|string|max:191',
            'status' => 'required|boolean',
            'city_id' => 'required|exists:cities,id',
        ]);
        $district = District::findOrFail($id);
        $data = $request->only(['name', 'status', 'city_id']);
        $district->update($data);
        return response()->json(['url' => route('admin.districts.index')]);
    }

    public function show($id)
    {
        $district = District::with('city')->findOrFail($id);
        $cities = City::all();
        return view('admin.districts.show', compact('district', 'cities'));
    }

    public function destroy($id)
    {
        District::findOrFail($id)->delete();
        return response()->json(['id' => $id]);
    }
}
