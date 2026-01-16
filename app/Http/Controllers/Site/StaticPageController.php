<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaticPageController extends Controller
{
    public function show($key)
    {
        if ($key === 'fqs') {
            $locale = app()->getLocale();
            $fqs = \App\Models\Fqs::orderBy('id')->get();
            return view('site.fqs', [
                'title' => __("site.fqs"),
                'fqs' => $fqs,
                'pageKey' => $key,
            ]);
        }
        $locale = app()->getLocale();
        $settingKey = "{$key}_ar";
        $setting = DB::table('site_settings')->where('key', $settingKey)->first();
        $logoSetting = DB::table('site_settings')->where('key', 'logo')->first();
        $logoUrl = $logoSetting ? asset('storage/images/' . $logoSetting->value) : null;
        if (!$setting) {
            abort(404);
        }
        return view('site.static_page', [
            'title' => __("site.$key"), // You can localize the title if needed
            'content' => $setting->value,
            'logo' => $logoUrl,
            'pageKey' => $key,
        ]);
    }
} 