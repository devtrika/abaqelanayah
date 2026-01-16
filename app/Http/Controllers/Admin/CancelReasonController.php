<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\cancelreasons\Store;
use App\Http\Requests\Admin\cancelreasons\Update;
use App\Models\CancelReason ;
use App\Traits\Report;


class CancelReasonController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $cancelreasons = CancelReason::search(request()->searchArray)->paginate(30);
            $html = view('admin.cancelreasons.table' ,compact('cancelreasons'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.cancelreasons.index');
    }

    public function create()
    {
        return view('admin.cancelreasons.create');
    }


    public function store(Store $request)
    {
        CancelReason::create($request->validated());
        Report::addToLog('  اضافه سبب الالغاء') ;
        return response()->json(['url' => route('admin.cancelreasons.index')]);
    }
    public function edit($id)
    {
        $cancelreason = CancelReason::findOrFail($id);
        return view('admin.cancelreasons.edit' , ['cancelreason' => $cancelreason]);
    }

    public function update(Update $request, $id)
    {
        $cancelreason = CancelReason::findOrFail($id)->update($request->validated());
        Report::addToLog('  تعديل سبب الالغاء') ;
        return response()->json(['url' => route('admin.cancelreasons.index')]);
    }

    public function show($id)
    {
        $cancelreason = CancelReason::findOrFail($id);
        return view('admin.cancelreasons.show' , ['cancelreason' => $cancelreason]);
    }
    public function destroy($id)
    {
        $cancelreason = CancelReason::findOrFail($id)->delete();
        Report::addToLog('  حذف سبب الالغاء') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (CancelReason::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من اسباب الالغاء') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
