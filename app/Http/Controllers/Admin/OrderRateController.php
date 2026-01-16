<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderRating;
use App\Traits\Report;
use App\Models\OrderRate ;
use App\Models\ShortVideo;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\orderrates\Store;
use App\Http\Requests\Admin\orderrates\Update;


class OrderRateController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $orderrates = OrderRating::search(request()->searchArray)->paginate(30);
            $html = view('admin.orderrates.table' ,compact('orderrates'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.orderrates.index');
    }

    public function create()
    {
        return view('admin.orderrates.create');
    }


 
    public function show($id)
    {
        $orderrate = OrderRating::findOrFail($id);

        return view('admin.orderrates.show' , ['orderrate' => $orderrate]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $orderrate = OrderRate::with('order', 'user')->findOrFail($id);
            $newStatus = $request->input('status');

            // Validate status
            $allowedStatuses = ['pending', 'approved', 'rejected'];
            if (!in_array($newStatus, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status provided'
                ], 400);
            }

            // Update status
            $orderrate->update(['status' => $newStatus]);

            // Log the action
            Report::addToLog('تحديث حالة التقييم إلى: ' . $newStatus);

            // Send notification to the user
            $orderNumber = $orderrate->order->order_number ?? $orderrate->order_id;
            $user = $orderrate->user;
            if ($user) {
                if ($newStatus === 'approved') {
                    $message = 'تمت الموافقة على تقييمك للطلب رقم ' . $orderNumber;
                } elseif ($newStatus === 'rejected') {
                    $message = 'تم رفض تقييمك للطلب رقم ' . $orderNumber;
                } else {
                    $message = null;
                }
                if ($message) {
                    $user->notify(new \App\Notifications\NotifyUser([
                        'title' => [
                            'ar' => 'تحديث حالة التقييم',
                            'en' => 'Order Rate Status Update'
                        ],
                        'body' => [
                            'ar' => $message,
                            'en' => $message
                        ],
                        'type' => 'order_rated',
                        'order_id' => $orderrate->order_id
                    ]));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $orderrate = OrderRate::findOrFail($id)->delete();
        Report::addToLog('  حذف التقييمات') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (OrderRate::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من التقييم') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    public function publishVideo(Request $request, $orderRateId)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id'
        ]);
    
        $orderRate = OrderRate::with('user')->findOrFail($orderRateId);
    
        // Ensure the media actually belongs to this order rate
        $media = $orderRate->getMedia('order_rate_videos')->where('id', $request->media_id)->first();
    
        if (!$media) {
            return response()->json(['message' => 'Selected video not found for this order'], 404);
        }
    
        if ($orderRate->shortVideo) {
            return response()->json(['message' => 'Video already published'], 400);
        }
    
        DB::beginTransaction();
    
        try {
            $shortVideo = ShortVideo::create([
'video_id' => 'vid-' . Str::random(length: 6),
                'order_rate_id' => $orderRate->id,
                'client_name' => $orderRate->user->name,
                'published_at' => now(),
                'user_id' => $orderRate->user->id,
            ]);
    
            $shortVideo
                ->addMedia($media->getPath())
                ->preservingOriginal()
                ->toMediaCollection('short_video');
    
            DB::commit();
    
            return response()->json(['message' => 'Video published successfully']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to publish video', 'error' => $e->getMessage()], 500);
        }
    }
    
    
}
