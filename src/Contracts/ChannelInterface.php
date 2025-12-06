<?php

namespace AdvancedNotifications\Contracts;

interface ChannelInterface
{
    /**
     * Send a notification via this channel.
     *
     * @param mixed $notifiable
     * @param mixed $notification
     * @return void
     */
    public function send($notifiable, $notification);
}
