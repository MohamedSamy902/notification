<?php

namespace App\Packages\FcmNotifications;

use Illuminate\Support\ServiceProvider;
use App\Packages\FcmNotifications\Contracts\NotificationServiceInterface;
use App\Packages\FcmNotifications\Contracts\FcmSenderInterface;
use App\Packages\FcmNotifications\Contracts\FcmAuthInterface;
use App\Packages\FcmNotifications\Services\NotificationService;
use App\Packages\FcmNotifications\Services\Firebase\FcmSenderService;
use App\Packages\FcmNotifications\Services\Firebase\FcmAuthService;

/**
 * Service Provider لـ FCM Notifications Package
 * 
 * مسؤول عن:
 * - تسجيل الخدمات (Services) في الـ Container
 * - ربط الـ Interfaces بالـ Implementations (Dependency Inversion)
 * - نشر ملفات الإعدادات والـ Migrations والـ Views
 */
class FcmNotificationServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الخدمات في الـ Container
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fcm-notifications.php',
            'fcm-notifications'
        );

        // ربط الـ Interfaces بالـ Implementations (Dependency Inversion Principle)
        $this->app->singleton(FcmAuthInterface::class, FcmAuthService::class);
        $this->app->singleton(FcmSenderInterface::class, FcmSenderService::class);
        $this->app->singleton(NotificationServiceInterface::class, NotificationService::class);
        
        // Backward compatibility - ربط الـ Classes مباشرة أيضاً
        $this->app->singleton(FcmAuthService::class);
        $this->app->singleton(FcmSenderService::class);
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap الخدمات
     * 
     * يتم تنفيذ هذه الدالة بعد تسجيل جميع Service Providers
     * 
     * @return void
     */
    public function boot()
    {
        // نشر ملف الإعدادات
        $this->publishes([
            __DIR__.'/../config/fcm-notifications.php' => config_path('fcm-notifications.php'),
        ], 'fcm-notifications-config');

        // نشر الـ Migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'fcm-notifications-migrations');

        // نشر Views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/fcm-notifications'),
        ], 'fcm-notifications-views');

        // نشر JavaScript Files
        $this->publishes([
            __DIR__.'/../resources/js' => public_path('vendor/fcm-notifications/js'),
        ], 'fcm-notifications-assets');

        // تحميل الـ Migrations تلقائياً
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // تحميل الـ Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'fcm-notifications');
    }
}
