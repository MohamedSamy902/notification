<?php

namespace AdvancedNotifications\Console\Commands;

use Illuminate\Console\Command;

class InstallPackage extends Command
{
    protected $signature = 'advanced-notifications:install';
    protected $description = 'Install the AdvancedNotifications package';

    public function handle()
    {
        $this->info('Installing AdvancedNotifications...');

        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => "AdvancedNotifications\AdvancedNotificationsServiceProvider",
            '--tag' => "advanced-notifications-config"
        ]);

        $this->info('Publishing migrations...');
        $this->call('vendor:publish', [
            '--provider' => "AdvancedNotifications\AdvancedNotificationsServiceProvider",
            '--tag' => "advanced-notifications-migrations"
        ]);

        $this->info('Publishing assets (views/js)...');
        // Assuming we might have public assets later, but for now views are enough
        // If there are compiled assets (JS/CSS), we should publish them too.
        // For now, we'll just publish views if user wants to customize them
        if ($this->confirm('Do you want to publish the views?', true)) {
            $this->call('vendor:publish', [
                '--provider' => "AdvancedNotifications\AdvancedNotificationsServiceProvider",
                '--tag' => "advanced-notifications-views"
            ]);
        }

        if ($this->confirm('Do you want to run the migrations now?', true)) {
            $this->call('migrate');
        }

        $this->info('AdvancedNotifications installed successfully.');
    }
}
