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
                // Handle multiple users (comma separated IDs)
                $recipientIds = explode(',', $request->input('recipient_id'));
                $userClass = config('auth.providers.users.model', 'App\\Models\\User');
                
                $users = $userClass::whereIn('id', $recipientIds)->get();

                foreach ($users as $user) {
                    // Persist notification to custom table for Dashboard History
                    $storedNotificationId = null;
                    try {
                        $storedNotification = AdvancedNotification::create([
                            'id' => \Illuminate\Support\Str::uuid(),
                            'type' => get_class($notification),
                            'notifiable_type' => get_class($user),
                            'notifiable_id' => $user->id,
                            'data' => $notification->toArray($user),
                            'read_at' => null,
                        ]);
                        $storedNotificationId = $storedNotification->id;
                    } catch (\Exception $e) {
                        Log::error('Failed to store notification: ' . $e->getMessage());
                    }

                    // Use Laravel's native notification system
                    $user->notify($notification);
                    
                    // Log Analytics (Sent)
                    foreach ($channels as $channel) {
                        app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
                            $storedNotificationId,
                            $channel,
                            'sent',
                            $user->id,
                            null, // Campaign ID
                            ['title' => $request->input('title')]
                        );
                    }
                }
                
                if ($users->isEmpty()) {
                    return back()->withErrors(['recipient_id' => 'No users found.']);
                }

            } elseif ($request->input('target_type') === 'topic') {
                // Send to Topic
                $topicName = $request->input('recipient_id');
                
                if (in_array('fcm', $channels)) {
                    $firebaseService = app(\AdvancedNotifications\Services\FirebaseService::class);
                    $firebaseService->sendToTopic(
                        $topicName,
                        $request->input('title'),
                        $request->input('body'),
                        $data['metadata']
                    );
                }
                Log::info("Notification sent to topic: $topicName");

            } elseif ($request->input('target_type') === 'all') {
                // Send to All Users (Chunked)
                $userClass = config('auth.providers.users.model', 'App\\Models\\User');
                
                // Dispatch a Job or process in chunks to avoid timeout
                // For simplicity in this context, we will use chunking directly, but ideally this should be a Job.
                $userClass::chunk(100, function ($users) use ($notification, $channels, $request) {
                    foreach ($users as $user) {
                        // 1. Store Notification
                        $storedNotificationId = null;
                        try {
                            $storedNotification = AdvancedNotification::create([
                                'id' => \Illuminate\Support\Str::uuid(),
                                'type' => get_class($notification),
                                'notifiable_type' => get_class($user),
                                'notifiable_id' => $user->id,
                                'data' => $notification->toArray($user),
                                'read_at' => null,
                            ]);
                            $storedNotificationId = $storedNotification->id;
                        } catch (\Exception $e) {
                            // Ignore storage error for mass send to keep speed
                        }

                        // 2. Send Notification
                        $user->notify($notification);

                        // 3. Log Analytics
                        foreach ($channels as $channel) {
                            app(\AdvancedNotifications\Services\AnalyticsService::class)->log(
                                $storedNotificationId,
                                $channel,
                                'sent',
                                $user->id,
                                null,
                                ['title' => $request->input('title')]
                            );
                        }
                    }
                });
                
                Log::info("Mass notification sent to all users.");
            }

            return redirect()->route('advanced-notifications.send.create')
                ->with('success', 'Notification sent successfully (or queued)!');

        } catch (\Exception $e) {
            Log::error($e);
            return back()->withErrors(['error' => 'Failed to send: ' . $e->getMessage()]);
        }
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json([]);
        }

        $userClass = config('auth.providers.users.model', 'App\\Models\\User');
        $nameColumn = config('advanced-notifications.user_name_column', 'name');

        $users = $userClass::where($nameColumn, 'LIKE', "%{$query}%")
            ->orWhere('id', $query)
            ->limit(10)
            ->get(['id', $nameColumn]);

        return response()->json($users->map(function ($user) use ($nameColumn) {
            return [
                'id' => $user->id,
                'name' => $user->{$nameColumn}
            ];
        }));
    }
}
