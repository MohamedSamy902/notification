<?php

namespace AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class FcmTopicSubscription extends Model
{
    protected $fillable = ['topic_id', 'subscriber_type', 'subscriber_id', 'token'];

    public function topic()
    {
        return $this->belongsTo(FcmTopic::class, 'topic_id');
    }

    public function subscriber()
    {
        return $this->morphTo();
    }
}
