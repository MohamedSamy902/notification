<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Models\NotificationAnalytic;

class AnalyticsService
{
    public function log($notificationId, $channel, $eventType, $userId = null, $campaignId = null, $metadata = [])
    {
        NotificationAnalytic::create([
            'notification_id' => $notificationId,
            'campaign_id' => $campaignId,
            'channel' => $channel,
            'event_type' => $eventType,
            'user_id' => $userId,
            'metadata' => $metadata,
        ]);
    }

    public function getStats($campaignId = null)
    {
        $query = NotificationAnalytic::query();

        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }

        return [
            'delivered' => $query->where('event', 'delivered')->count(),
            'read' => $query->where('event', 'read')->count(),
            'clicked' => $query->where('event', 'clicked')->count(),
            'failed' => $query->where('event', 'failed')->count(),
        ];
    }
}
