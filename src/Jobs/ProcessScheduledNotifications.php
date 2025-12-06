<?php

namespace AdvancedNotifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use AdvancedNotifications\Models\ScheduledNotification;
use AdvancedNotifications\Facades\AdvancedNotifications;

class ProcessScheduledNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scheduledNotification;

    public function __construct(ScheduledNotification $scheduledNotification)
    {
        $this->scheduledNotification = $scheduledNotification;
    }

    public function handle()
    {
        try {
            // Logic to fetch user/recipient and send notification
            // This is a simplified example
            $recipientClass = $this->scheduledNotification->recipient_type;
            $recipient = $recipientClass::find($this->scheduledNotification->recipient_id);

            if ($recipient) {
                // We need to construct the notification object from the template
                // For now, we'll assume a GenericNotification class exists or we use the TemplateService to render
                // and then send via ChannelManager
                
                // $notification = new GenericNotification($this->scheduledNotification->template_id, $this->scheduledNotification->data);
                // AdvancedNotifications::send($recipient, $notification);
                
                $this->scheduledNotification->update(['status' => 'sent']);
            } else {
                $this->scheduledNotification->update(['status' => 'failed', 'error_message' => 'Recipient not found']);
            }
        } catch (\Exception $e) {
            $this->scheduledNotification->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}
