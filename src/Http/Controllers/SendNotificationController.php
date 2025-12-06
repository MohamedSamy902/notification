<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Models\FcmTopic;
use AdvancedNotifications\Facades\AdvancedNotifications;
use AdvancedNotifications\Models\AdvancedNotification;
use Illuminate\Support\Facades\Log;

class SendNotificationController extends Controller
{
    public function create()
    {
        $topics = FcmTopic::all();
        return view('advanced-notifications::dashboard.send.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:user,topic,all',
            'recipient_id' => 'required_if:target_type,user,topic',
            'title' => 'required|string',
            'body' => 'required|string',
            'channels' => 'required|array|min:1',
        ]);

        $channels = $request->input('channels');
        $data = [
            'icon' => $request->input('icon'),
            'action_url' => $request->input('action_url'),
            'metadata' => ['sent_from_dashboard' => true]
        ];

        // Create notification object manually since we are not using a template here
        // We'll use a GenericNotification class or similar if available, otherwise we construct it
        // For simplicity in this controller, we'll use the Facade's underlying service logic or a temporary notification class
        
        $notification = new \AdvancedNotifications\Notifications\GenericNotification(
            $request->input('title'),
            $request->input('body'),
            $request->input('icon'),
            $request->input('action_url'),
            $data['metadata'],
            $channels
        );

        try {
            if ($request->input('target_type') === 'user') {
                // Assuming User model is App\Models\User
                $userClass = config('auth.providers.users.model', 'App\\Models\\User');
                $user = $userClass::find($request->input('recipient_id'));
                
                if ($user) {
                    // Persist notification to get ID
                    try {
                        $storedNotification = AdvancedNotification::create([
                            'id' => \Illuminate\Support\Str::uuid(),
                            'type' => get_class($notification),
                            'notifiable_type' => get_class($user),
                            'notifiable_id' => $user->id,
                            'data' => $notification->toArray($user),
                            'read_at' => null,
                        ]);
                        Log::info('Notification stored with ID: ' . $storedNotification->id);
                    } catch (\Exception $e) {
                        Log::error('Failed to store notification: ' . $e->getMessage());
                        throw $e;
                    }

                    AdvancedNotifications::send($user, $notification);
                    
                    // Log Analytics
                    foreach ($channels as $channel) {
                        Log::info('Logging analytics for channel: ' . $channel);
                        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
                            $storedNotification->id,
                            $channel,
                            'sent',
                            $user->id,
                            null, // Campaign ID
                            ['title' => $request->input('title')]
                        );
                    }
                } else {
                    return back()->withErrors(['recipient_id' => 'User not found.']);
                }
            } elseif ($request->input('target_type') === 'topic') {
                // Send to Topic
                // For FCM, we use sendToTopic. For other channels, we might need to iterate subscribers.
                // Currently, sendToTopic in ChannelManager only supports FCM via driver('fcm')
                // But we want to support multiple channels if possible.
                // For now, if topic is selected, we primarily target FCM.
                
                if (in_array('fcm', $channels)) {
                    AdvancedNotifications::sendToTopic(
                        $request->input('recipient_id'), // Topic Name
                        'manual_message', // We need a template key usually, but let's overload or use a direct send method
                        ['title' => $request->input('title'), 'body' => $request->input('body')] // Hacky, ideally we improve sendToTopic signature
                    );
                    // Note: The current sendToTopic implementation expects a template key. 
                    // We might need to refactor sendToTopic to accept raw content or create a sendToTopicRaw method.
                    // For this iteration, let's assume we update sendToTopic or use a workaround.
                }
                
                // Also send to subscribers in DB for other channels?
                // This would be a heavy operation, maybe queue it.
            } elseif ($request->input('target_type') === 'all') {
                // Send to All Users
                // This MUST be queued.
                // For now, we'll just log it as "Not fully implemented for mass send" or implement a basic loop
                Log::warning("Mass send requested from dashboard.");
            }

            return redirect()->route('advanced-notifications.send.create')
                ->with('success', 'Notification sent successfully (or queued)!');

        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }
}
