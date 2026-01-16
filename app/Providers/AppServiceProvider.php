<?php

namespace App\Providers;

use App\Http\View\Composers\HeaderComposer;
use App\Models\SiteSetting;
use App\Models\Social;
use App\Services\Responder;
use App\Services\SettingService;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

    protected $settings;
    protected $socials;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        // Register the Responder service
        $this->app->singleton('responder', function () {
            return new Responder();
        });
    }

    public function boot() {
        Paginator::useBootstrap();

        Schema::defaultStringLength(191);

        // Register View Composers
        View::composer('website.partials.header', HeaderComposer::class);

        try {
            $this->settings = Cache::rememberForever('settings', function () {
                return SettingService::appInformations(SiteSetting::pluck('value', 'key'));
            });
            $this->socials = Cache::rememberForever('socials', function () {
                return Social::get();
            });

            // view()->composer('admin.*', function ($view) {
            view()->composer('*', function ($view) {
                $view->with([
                    'settings' => $this->settings,
                ]);
            });

        // view()->composer('site.*', function ($view) {
        //     $view->with([
        //         'settings' => $this->settings,
        //         'socials'  => $this->socials,
        //     ]);
        // });



        } catch (Exception $e) {
            // echo ('app service provider exception :::::::::: ' . $e->getMessage());
        }



        // -------------- lang ---------------- \\
        app()->singleton('lang', function () {
            // Respect explicit user selection first
            if (session()->has('lang')) {
                return session('lang');
            }

            // Fallback to browser preference among allowed languages
            try {
                $available = languages(); // e.g. ['ar','en'] from settings
                $preferred = request()->getPreferredLanguage($available);
                if ($preferred && in_array($preferred, $available)) {
                    return $preferred;
                }
            } catch (\Throwable $e) {
                // silent fallback
            }

            // Final fallback to app default locale
            return config('app.locale', 'ar');
        });
        // -------------- lang ---------------- \\
    }
}
