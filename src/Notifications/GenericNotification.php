<?php

namespace AdvancedNotifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $body;
    public $icon;
    public $actionUrl;
    public $data;
    public $channels;

    public function __construct($title, $body, $icon = null, $actionUrl = null, $data = [], $channels = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
        $this->actionUrl = $actionUrl;
        $this->data = $data;
        $this->channels = $channels;
    }

    public function via($notifiable)
    {
        $channels = $this->channels ?? config('advanced-notifications.default_channels');
        
        return array_map(function ($channel) {
            if ($channel === 'fcm') {
                return \AdvancedNotifications\Channels\FcmChannel::class;
            }
            if ($channel === 'realtime') {
                return 'broadcast';
            }
            return $channel;
        }, $channels);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'action_url' => $this->actionUrl,
            'data' => $this->data,
        ];
    }

    public function toFcm($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => array_merge($this->data, [
                'icon' => $this->icon,
                'action_url' => $this->actionUrl,
            ]),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage([
            'title' => $this->title,
            'body' => $this->body,
            'icon' => $this->icon,
            'action_url' => $this->actionUrl,
            'data' => $this->data,
        ]);
    }
}
