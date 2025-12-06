<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Models\NotificationCampaign;
use AdvancedNotifications\Jobs\ProcessCampaign;

class CampaignService
{
    public function createCampaign($name, $templateId, $filters, $scheduledAt = null)
    {
        return NotificationCampaign::create([
            'name' => $name,
            'template_id' => $templateId,
            'filters' => $filters,
            'scheduled_at' => $scheduledAt,
            'status' => $scheduledAt ? 'scheduled' : 'draft',
        ]);
    }

    public function startCampaign(NotificationCampaign $campaign)
    {
        $campaign->update(['status' => 'running', 'started_at' => now()]);
        ProcessCampaign::dispatch($campaign);
    }
}
