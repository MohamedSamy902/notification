<?php

namespace AdvancedNotifications\Services;

use Illuminate\Support\Facades\Broadcast;
use AdvancedNotifications\Events\NotificationCreated;

class RealtimeBroadcastService
{
    public function broadcast($notifiable, $notification)
    {
        event(new \AdvancedNotifications\Events\NotificationCreated($notifiable, $notification));
    }

    public function send($notifiable, $notification)
    {
        $this->broadcast($notifiable, $notification);
    }
}
