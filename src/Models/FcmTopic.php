<?php

namespace AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class FcmTopic extends Model
{
    protected $fillable = ['name', 'display_name', 'description'];

    public function subscriptions()
    {
        return $this->hasMany(FcmTopicSubscription::class, 'topic_id');
    }
}
