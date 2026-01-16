<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\rates\Store;
use App\Http\Requests\Admin\rates\Update;
use App\Models\Rate;
use App\Models\ShortVideo;
use App\Traits\Report;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class RateController extends Controller
{
    public function index($id = null)
    {
        if (request()->ajax()) {
            $rates = Rate::search(request()->searchArray)->paginate(30);
            $html = view('admin.rates.table' ,compact('rates'))->render() ;
            return response()->json(['html' => $html]);
        }
        return view('admin.rates.index');
    }

    public function show($id)
    {
        $rate = Rate::with(['user', 'rateable'])->findOrFail($id);
        return view('admin.rates.show' , ['rate' => $rate]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $rate = Rate::with(['user', 'rateable'])->findOrFail($id);
            $newStatus = $request->input('status');

            // Validate status
            $allowedStatuses = ['pending', 'approved', 'rejected'];
            if (!in_array($newStatus, $allowedStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.invalid_status_provided')
                ], 400);
            }

            // Update status
            $rate->update(['status' => $newStatus]);

            // Log the action
            Report::addToLog('تحديث حالة التقييم إلى: ' . $newStatus);

            // Send notification to the user
            $user = $rate->user;
            if ($user) {
                $rateableType = class_basename($rate->rateable_type);
                $rateableName = $rate->rateable->commercial_name ?? $rate->rateable->name ?? 'Unknown';

                if ($newStatus === 'approved') {
                    $message = "تمت الموافقة على تقييمك لـ {$rateableName}";
                } elseif ($newStatus === 'rejected') {
                    $message = "تم رفض تقييمك لـ {$rateableName}";
                } else {
                    $message = null;
                }

                if ($message) {
                    $user->notify(new \App\Notifications\NotifyUser([
                        'title' => [
                            'ar' => 'تحديث حالة التقييم',
                            'en' => 'Rate Status Update'
                        ],
                        'body' => [
                            'ar' => $message,
                            'en' => $message
                        ],
                        'type' => 'rate_status',
                        'rate_id' => $rate->id
                    ]));
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('admin.status_updated_successfully'),
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function publishVideo(Request $request, $rateId)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id'
        ]);

        try {
            $rate = Rate::with('user')->findOrFail($rateId);

            // Ensure the media actually belongs to this rate
            $media = $rate->getMedia('rate-media')->where('id', $request->media_id)->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.video_not_found_for_rate')
                ], 404);
            }

            // Check if video is actually a video file
            if (!str_starts_with($media->mime_type, 'video/')) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.selected_media_not_video')
                ], 400);
            }

            // Check if already published
            $existingShortVideo = ShortVideo::where('rate_id', $rate->id)->first();
            if ($existingShortVideo) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin.video_already_published')
                ], 400);
            }

            DB::beginTransaction();

            $shortVideo = ShortVideo::create([
                'video_id' => 'vid-' . Str::random(6),
                'rate_id' => $rate->id,
                'client_name' => $rate->user->name,
                'published_at' => now(),
                'user_id' => $rate->user->id,
                'is_active' => true
            ]);

            $shortVideo
                ->addMedia($media->getPath())
                ->preservingOriginal()
                ->toMediaCollection('short_video');

            DB::commit();

            // Log the action
            Report::addToLog('نشر فيديو تقييم كـ Short Video');

            return response()->json([
                'success' => true,
                'message' => __('admin.video_published_successfully')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('admin.error_occurred') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $rate = Rate::findOrFail($id)->delete();
        Report::addToLog('  حذف تقييم') ;
        return response()->json(['id' =>$id]);
    }

    public function destroyAll(Request $request)
    {
        $requestIds = json_decode($request->data);
        
        foreach ($requestIds as $id) {
            $ids[] = $id->id;
        }
        if (Rate::whereIntegerInRaw('id',$ids)->get()->each->delete()) {
            Report::addToLog('  حذف العديد من التقييمات') ;
            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }
}
