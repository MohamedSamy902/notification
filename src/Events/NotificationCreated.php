<?php

namespace AdvancedNotifications\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifiable;
    public $notification;

    public function __construct($notifiable, $notification)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        // Broadcast to the user's private channel
        // Usually App.Models.User.{id}
        return new PrivateChannel('App.Models.User.' . $this->notifiable->id);
    }

    public function broadcastWith()
    {
        $data = $this->notification->data ?? [];
        
        // If the notification has toArray method, use it to get standard fields
        if (method_exists($this->notification, 'toArray')) {
            $arrayData = $this->notification->toArray($this->notifiable);
            // Merge array data but prefer specific properties if they exist on the object
            $data = array_merge($data, $arrayData);
        }

        // Explicitly check for public properties if not in data
        foreach (['title', 'body', 'icon', 'action_url'] as $field) {
            if (!isset($data[$field]) && isset($this->notification->$field)) {
                $data[$field] = $this->notification->$field;
            }
            // Handle camelCase properties like actionUrl
            $camelField = \Illuminate\Support\Str::camel($field);
            if (!isset($data[$field]) && isset($this->notification->$camelField)) {
                $data[$field] = $this->notification->$camelField;
            }
        }

        return [
            'id' => $this->notification->id ?? null,
            'type' => get_class($this->notification),
            'data' => $data,
            'read_at' => null,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
