<?php

namespace App\Providers;

use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Channels\FirebaseChannel;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            $service = new NotificationService();
            
            // Set default channels from config
            $defaultChannels = config('notifications.default_channels', ['database', 'firebase']);
            $service->setDefaultChannels($defaultChannels);
            
            return $service;
        });

        // Register the notification service as a singleton
        $this->app->alias(NotificationService::class, 'notification.service');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register custom notification channels
        Notification::extend('firebase', function ($app) {
            return new FirebaseChannel();
        });

        // Register console commands if running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\CleanupNotifications::class,
                \App\Console\Commands\SendTestNotification::class,
            ]);
        }
    }
}
