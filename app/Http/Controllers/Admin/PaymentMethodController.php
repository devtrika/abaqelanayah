<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\paymentmethods\Store;
use App\Http\Requests\Admin\paymentmethods\Update;
use App\Models\PaymentMethod ;
use App\Traits\Report;


class PaymentMethodController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $paymentmethods = PaymentMethod::search(request()->searchArray)->paginate(30);
            $html = view('admin.paymentmethods.table' ,compact('paymentmethods'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.paymentmethods.index');
    }


    public function destroy($id)
    {
        $paymentmethod = PaymentMethod::findOrFail($id)->delete();
        Report::addToLog('  حذف طريقهالدفع') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);

        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (PaymentMethod::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من طرقالدفع') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    public function edit($id)
    {
        $paymentmethod = PaymentMethod::findOrFail($id);
        return view('admin.paymentmethods.edit', compact('paymentmethod'));
    }

    public function update(Request $request, $id)
    {
        $paymentmethod = PaymentMethod::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $oldImage = $paymentmethod->image;

        if ($request->hasFile('image')) {
            $filename = $paymentmethod->uploadAllTyps($request->file('image'), PaymentMethod::IMAGEPATH);
            $newPath = 'storage/images/' . PaymentMethod::IMAGEPATH . '/' . $filename;
            $paymentmethod->image = $newPath;
            $paymentmethod->save();

            // delete old image if exists and belongs to the same directory
            if (!empty($oldImage) && strpos($oldImage, 'storage/images/' . PaymentMethod::IMAGEPATH) === 0) {
                $basename = basename($oldImage);
                if ($basename && $basename !== 'default.png') {
                    $paymentmethod->deleteFile($basename, PaymentMethod::IMAGEPATH);
                }
            }
        }

        Report::addToLog('  تعديل طريقهالدفع');
        return response()->json(['url' => route('admin.paymentmethods.index')]);
    }

}
