<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\problems\Store;
use App\Http\Requests\Admin\problems\Update;
use App\Models\Problem ;
use App\Traits\Report;


class ProblemController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $problems = Problem::search(request()->searchArray)->paginate(30);
            $html = view('admin.problems.table' ,compact('problems'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.problems.index');
    }

    public function create()
    {
        return view('admin.problems.create');
    }


    public function store(Store $request)
    {
        Problem::create($request->validated());
        Report::addToLog('  اضافه مشكله') ;
        return response()->json(['url' => route('admin.problems.index')]);
    }
    public function edit($id)
    {
        $problem = Problem::findOrFail($id);
        return view('admin.problems.edit' , ['problem' => $problem]);
    }

    public function update(Update $request, $id)
    {
        $problem = Problem::findOrFail($id)->update($request->validated());
        Report::addToLog('  تعديل مشكله') ;
        return response()->json(['url' => route('admin.problems.index')]);
    }

    public function show($id)
    {
        $problem = Problem::findOrFail($id);
        return view('admin.problems.show' , ['problem' => $problem]);
    }
    public function destroy($id)
    {
        $problem = Problem::findOrFail($id)->delete();
        Report::addToLog('  حذف مشكله') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (Problem::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من مشاكل') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
