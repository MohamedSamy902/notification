<?php

namespace AdvancedNotifications\Channels;

use Illuminate\Notifications\Notification;
use AdvancedNotifications\Services\FirebaseService;

class FcmChannel
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function send($notifiable, Notification $notification)
    {
        $this->firebaseService->send($notifiable, $notification);
    }
}
