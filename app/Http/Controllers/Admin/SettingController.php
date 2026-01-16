<?php

namespace App\Http\Controllers\Admin;

use App\Traits\Report;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use App\Services\SettingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Country ;

class SettingController extends Controller
{
    public function index(){
        $data = Cache::rememberForever('settings', function () {
            return SettingService::appInformations(SiteSetting::pluck('value', 'key'));
        });
        $countries = Country::orderBy('id','ASC')->get();

        // Get settings with media relations for image display
        $imageSettings = SiteSetting::whereIn('key', [
            'logo', 'fav_icon', 'default_user', 'intro_loader', 'intro_logo',
            'about_image_1', 'about_image_2', 'login_background', 'profile_cover', 'no_data'
        ])->with('media')->get()->keyBy('key');

        return view('admin.settings.index', compact('data', 'countries', 'imageSettings'));
    }


    public function update(Request $request){
        Cache::forget('settings');

        $uploadErrors = [];
        $uploadSuccesses = [];

        // Process all request data
        foreach ($request->all() as $key => $val) {
            // Handle image removals
            if (str_starts_with($key, 'remove_') && $val == 1) {
                $imgKey = substr($key, 7);
                $setting = SiteSetting::where('key', $imgKey)->first();
                if ($setting) {
                    $setting->clearMediaCollection($imgKey);
                    // Set default image path instead of null
                    $defaultImages = [
                        'logo' => 'storage/images/default.png',
                        'fav_icon' => 'storage/images/default.png',
                        'profile_cover' => 'storage/images/default.png',
                        'default_user' => 'storage/images/default.png',
                    ];
                    $default = $defaultImages[$imgKey] ?? null;
                    $setting->update(['value' => $default]);
                }
                continue;
            }
            // Handle image uploads using Spatie Media Library
            if (in_array($key, ['logo', 'fav_icon', 'default_user', 'intro_loader', 'intro_logo', 'about_image_2', 'about_image_1', 'login_background', 'profile_cover', 'no_data'])) {
                if ($val && is_file($val)) {
                    try {
                        // Get or create the setting record
                        $setting = SiteSetting::firstOrCreate(['key' => $key], ['key' => $key, 'value' => '']);

                        // Validate file before processing
                        if (!$setting->canAcceptFile($val, $key)) {
                            $uploadErrors[] = "File {$val->getClientOriginalName()} was rejected for {$key}. Please check file type and size.";
                            Log::warning('File rejected for collection: ' . $key, [
                                'file_name' => $val->getClientOriginalName(),
                                'file_size' => $val->getSize(),
                                'mime_type' => $val->getMimeType(),
                                'collection' => $key
                            ]);
                            continue;
                        }

                        // Clear existing media in this collection
                        $setting->clearMediaCollection($key);

                        // Determine filename based on key
                        $fileName = match($key) {
                            'default_user' => 'default.png',
                            'no_data' => 'no_data.png',
                            default => time() . '_' . $key . '.' . $val->getClientOriginalExtension()
                        };

                        // Add the new media to the collection
                        $media = $setting->addMediaFromRequest($key)
                            ->usingName($key)
                            ->usingFileName($fileName)
                            ->toMediaCollection($key);

                        // Update the value field with the media URL
                        $setting->update(['value' => $media->getUrl()]);
                        $uploadSuccesses[] = "Successfully uploaded {$val->getClientOriginalName()} for {$key}";

                    } catch (\Exception $e) {
                        $uploadErrors[] = "Failed to upload {$val->getClientOriginalName()} for {$key}: {$e->getMessage()}";
                        // Log the error for debugging
                        Log::error('Media upload failed for key: ' . $key, [
                            'error' => $e->getMessage(),
                            'file_name' => $val->getClientOriginalName(),
                            'file_size' => $val->getSize(),
                            'mime_type' => $val->getMimeType(),
                            'setting_id' => $setting->id ?? 'not_created'
                        ]);

                        // Continue with the loop, don't break the entire process
                        continue;
                    }
                }
            }
            // Handle non-file inputs
            else if ($val) {
                SiteSetting::updateOrCreate(['key' => $key], ['key' => $key, 'value' => $val]);
            }
        }

        // Handle boolean settings
        if ($request->is_production) {
            SiteSetting::where('key', 'is_production')->update(['value' => 1]);
        } else {
            SiteSetting::where('key', 'is_production')->update(['value' => 0]);
        }

        if ($request->registeration_availability) {
            SiteSetting::where('key', 'registeration_availability')->update(['value' => 1]);
        } else {
            SiteSetting::where('key', 'registeration_availability')->update(['value' => 0]);
        }

        // Refresh the cache
        Cache::rememberForever('settings', function () {
            return SettingService::appInformations(SiteSetting::pluck('value', 'key'));
        });

        Report::addToLog('تعديل الاعدادت');

        // Prepare feedback message
        $message = 'تم الحفظ';
        if (!empty($uploadErrors)) {
            $message .= '. However, some files failed to upload: ' . implode(', ', $uploadErrors);
        }
        if (!empty($uploadSuccesses)) {
            $message .= '. Successfully uploaded: ' . implode(', ', $uploadSuccesses);
        }

        return back()->with('success' , __('admin.update_successfullay'));
    }
}
