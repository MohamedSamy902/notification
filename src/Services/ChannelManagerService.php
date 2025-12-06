<?php

namespace AdvancedNotifications\Services;

use Illuminate\Support\Manager;
use AdvancedNotifications\Contracts\ChannelInterface;

class ChannelManagerService extends Manager implements ChannelInterface
{
    protected $realtimeService;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->realtimeService = app(RealtimeBroadcastService::class);
    }

    public function getDefaultDriver()
    {
        return $this->config->get('advanced-notifications.default_channels')[0] ?? 'database';
    }

    public function createDatabaseDriver()
    {
        return new InternalNotificationService();
    }

    public function createFcmDriver()
    {
        return new FirebaseService();
    }

    public function createRealtimeDriver()
    {
        return $this->realtimeService;
    }

    public function send($notifiable, $notification)
    {
        foreach ($this->getChannels($notification) as $channel) {
            $this->driver($channel)->send($notifiable, $notification);
        }
    }

    protected function getChannels($notification)
    {
        if (method_exists($notification, 'via')) {
            return $notification->via($notifiable ?? null);
        }

        return $this->config->get('advanced-notifications.default_channels');
    }
    public function sendFromTemplate($notifiable, $templateKey, $data = [], $channels = null)
    {
        $templateService = app(TemplateService::class);
        $content = $templateService->render($templateKey, $data);

        if (!$content) {
            return;
        }

        $notification = new \AdvancedNotifications\Notifications\GenericNotification(
            $content['title'],
            $content['body'],
            $content['icon'],
            $content['action_url'],
            $content['metadata'] ?? [],
            $channels
        );

        $this->send($notifiable, $notification);
    }

    // Proxy methods for FCM Topics
    public function subscribeToTopic($tokens, $topic)
    {
        return $this->driver('fcm')->subscribeToTopic($tokens, $topic);
    }

    public function unsubscribeFromTopic($tokens, $topic)
    {
        return $this->driver('fcm')->unsubscribeFromTopic($tokens, $topic);
    }

    public function sendToTopic($topic, $templateKey, $data = [])
    {
        $templateService = app(TemplateService::class);
        $content = $templateService->render($templateKey, $data);

        if (!$content) {
            return false;
        }

        return $this->driver('fcm')->sendToTopic(
            $topic, 
            $content['title'], 
            $content['body'], 
            $content['metadata'] ?? []
        );
    }
}
