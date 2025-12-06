<?php

namespace AdvancedNotifications\Services;

use Illuminate\Support\Facades\Cache;

class RateLimitingService
{
    public function checkLimit($user, $channel)
    {
        if (!config('advanced-notifications.rate_limit.enabled')) {
            return true;
        }

        $key = "notification_limit:{$user->id}:{$channel}";
        $maxAttempts = config('advanced-notifications.rate_limit.max_attempts', 5);
        $decayMinutes = config('advanced-notifications.rate_limit.decay_minutes', 60);

        if (Cache::has($key) && Cache::get($key) >= $maxAttempts) {
            return false;
        }

        return true;
    }

    public function increment($user, $channel)
    {
        if (!config('advanced-notifications.rate_limit.enabled')) {
            return;
        }

        $key = "notification_limit:{$user->id}:{$channel}";
        $decayMinutes = config('advanced-notifications.rate_limit.decay_minutes', 60);

        if (Cache::has($key)) {
            Cache::increment($key);
        } else {
            Cache::put($key, 1, now()->addMinutes($decayMinutes));
        }
    }
}
