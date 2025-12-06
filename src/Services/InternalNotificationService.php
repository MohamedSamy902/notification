<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Contracts\ChannelInterface;
use AdvancedNotifications\Models\AdvancedNotification;

class InternalNotificationService implements ChannelInterface
{
    public function send($notifiable, $notification)
    {
        $data = $notification->toArray($notifiable);

        AdvancedNotification::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'data' => $data,
            'read_at' => null,
            'category' => $data['category'] ?? null,
            'icon' => $data['icon'] ?? null,
            'color' => $data['color'] ?? null,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }
}
