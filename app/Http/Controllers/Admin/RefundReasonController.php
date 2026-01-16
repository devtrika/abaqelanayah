<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\cancelreasons\Store as CancelreasonsStore;
use App\Http\Requests\Admin\cancelreasons\Update as CancelreasonsUpdate;
use App\Http\Requests\Admin\refundreasons\Store;
use App\Http\Requests\Admin\refundreasons\Update;
use App\Models\RefundReason;
use App\Traits\Report;

class RefundReasonController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $refundreasons = RefundReason::search(request()->searchArray)->paginate(30);
            $html = view('admin.refundreasons.table', compact('refundreasons'))->render();
            return response()->json(['html' => $html]);
        }
        return view('admin.refundreasons.index');
    }

    public function create()
    {
        return view('admin.refundreasons.create');
    }

    public function store(CancelreasonsStore $request)
    {
        RefundReason::create($request->validated());
        Report::addToLog('اضافة سبب الاسترجاع');
        return response()->json(['url' => route('admin.refundreasons.index')]);
    }

    public function edit($id)
    {
        $refundreason = RefundReason::findOrFail($id);
        return view('admin.refundreasons.edit', ['refundreason' => $refundreason]);
    }

    public function update(CancelreasonsUpdate $request, $id)
    {
        RefundReason::findOrFail($id)->update($request->validated());
        Report::addToLog('تعديل سبب الاسترجاع');
        return response()->json(['url' => route('admin.refundreasons.index')]);
    }

    public function show($id)
    {
        $refundreason = RefundReason::findOrFail($id);
        return view('admin.refundreasons.show', ['refundreason' => $refundreason]);
    }

    public function destroy($id)
    {
        RefundReason::findOrFail($id)->delete();
        Report::addToLog('حذف سبب الاسترجاع');
        return response()->json(['id' => $id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        $ids = [];
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (RefundReason::whereIntegerInRaw('id', $ids)->get()->each->delete()) {
            Report::addToLog('حذف العديد من اسباب الاسترجاع');
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}