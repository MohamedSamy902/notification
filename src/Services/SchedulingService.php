<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Models\ScheduledNotification;
use AdvancedNotifications\Jobs\ProcessScheduledNotifications;

class SchedulingService
{
    public function schedule($recipientType, $recipientId, $templateId, $sendAt, $data = [], $channel = null, $campaignId = null)
    {
        return ScheduledNotification::create([
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'template_id' => $templateId,
            'send_at' => $sendAt,
            'data' => $data,
            'channel' => $channel,
            'campaign_id' => $campaignId,
            'status' => 'pending',
        ]);
    }

    public function dispatchDueNotifications()
    {
        $dueNotifications = ScheduledNotification::where('status', 'pending')
            ->where('send_at', '<=', now())
            ->get();

        foreach ($dueNotifications as $notification) {
            ProcessScheduledNotifications::dispatch($notification);
        }
    }
}
