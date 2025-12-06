<?php

namespace AdvancedNotifications\Traits;

use AdvancedNotifications\Models\DeviceToken;
use Illuminate\Notifications\Notifiable;

trait HasAdvancedNotifications
{
    use Notifiable;

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'user_id');
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->deviceTokens()->pluck('token')->toArray();
    }
}
