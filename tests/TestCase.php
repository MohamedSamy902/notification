<?php

namespace AdvancedNotifications\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use AdvancedNotifications\AdvancedNotificationsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            AdvancedNotificationsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        
        /*
        $migration = include __DIR__.'/../database/migrations/create_advanced_notifications_table.php.stub';
        $migration->up();
        */
    }
}
