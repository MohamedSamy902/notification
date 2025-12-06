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

    public function notification()
    {
        return $this->belongsTo(AdvancedNotification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model', 'App\\Models\\User'), 'user_id');
    }
}
