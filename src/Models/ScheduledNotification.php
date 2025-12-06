<?php

namespace AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledNotification extends Model
{
    protected $fillable = [
        'campaign_id',
        'recipient_type',
        'recipient_id',
        'channel',
        'template_id',
        'data',
        'send_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'data' => 'array',
        'send_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(NotificationCampaign::class);
    }

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class);
    }
}
