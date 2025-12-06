<?php

namespace AdvancedNotifications\Facades;

use Illuminate\Support\Facades\Facade;

class AdvancedNotifications extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'advanced-notifications';
    }
}
