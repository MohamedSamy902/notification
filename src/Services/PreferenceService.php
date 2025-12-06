<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Models\UserNotificationPreference;

class PreferenceService
{
    public function wantsNotification($user, $channel, $type = null)
    {
        // Check global channel preference
        $preference = UserNotificationPreference::where('user_id', $user->id)
            ->where('channel', $channel)
            ->first();

        if ($preference && !$preference->is_enabled) {
            return false;
        }

        // Check quiet hours
        if ($preference && $preference->quiet_hours_start && $preference->quiet_hours_end) {
            $now = now()->format('H:i:s');
            if ($now >= $preference->quiet_hours_start && $now <= $preference->quiet_hours_end) {
                return false;
            }
        }

        return true;
    }

    public function updatePreference($user, $channel, $isEnabled, $quietStart = null, $quietEnd = null)
    {
        return UserNotificationPreference::updateOrCreate(
            ['user_id' => $user->id, 'channel' => $channel],
            [
                'is_enabled' => $isEnabled,
                'quiet_hours_start' => $quietStart,
                'quiet_hours_end' => $quietEnd,
            ]
        );
    }
}
