<?php

namespace AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationAnalytic extends Model
{
    protected $fillable = [
        'notification_id',
        'campaign_id',
        'channel',
        'event_type',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
