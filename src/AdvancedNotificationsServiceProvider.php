<?php

namespace AdvancedNotifications;

use Illuminate\Support\ServiceProvider;
use AdvancedNotifications\Services\ChannelManagerService;
use AdvancedNotifications\Services\FirebaseService;
use AdvancedNotifications\Services\TemplateService;

class AdvancedNotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/advanced-notifications.php', 'advanced-notifications');

        $this->app->singleton('advanced-notifications', function ($app) {
            return new ChannelManagerService($app);
        });

        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });

        $this->app->singleton(TemplateService::class, function ($app) {
            return new TemplateService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/advanced-notifications.php' => config_path('advanced-notifications.php'),
            ], 'advanced-notifications-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'advanced-notifications-migrations');

            $this->commands([
                \AdvancedNotifications\Console\Commands\InstallPackage::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'advanced-notifications');
    }
}
