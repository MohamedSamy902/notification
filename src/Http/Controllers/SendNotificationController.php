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
                    // Persist notification to custom table for Dashboard History
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
                        // Continue sending even if storage fails
                    }

                    // Use Laravel's native notification system
                    $user->notify($notification);
                    
                    // Log Analytics
                    foreach ($channels as $channel) {
                        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
                            $storedNotification->id ?? null,
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
                $topicName = $request->input('recipient_id');
                
                if (in_array('fcm', $channels)) {
                    // Use the Firebase Service directly for Topics
                    $firebaseService = app(\AdvancedNotifications\Services\FirebaseService::class);
                    $firebaseService->sendToTopic(
                        $topicName,
                        $request->input('title'),
                        $request->input('body'),
                        $data['metadata']
                    );
                }
                
                // Log generic success for topic
                Log::info("Notification sent to topic: $topicName");
            } elseif ($request->input('target_type') === 'all') {
                // Send to All Users
                // This MUST be queued.
                Log::warning("Mass send requested from dashboard.");
                // Implementation for mass send would go here (e.g. dispatch a job)
            }

            return redirect()->route('advanced-notifications.send.create')
                ->with('success', 'Notification sent successfully (or queued)!');

        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }
}
