<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Models\FcmTopic;

class TopicController extends Controller
{
    public function index()
    {
        $topics = FcmTopic::withCount('subscriptions')->get();
        return view('advanced-notifications::dashboard.topics.index', compact('topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:fcm_topics,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        FcmTopic::create($request->only('name', 'display_name', 'description'));

        return redirect()->route('advanced-notifications.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    public function show($id)
    {
        $topic = FcmTopic::with('subscriptions.subscriber')->findOrFail($id);
        return view('advanced-notifications::dashboard.topics.show', compact('topic'));
    }

    public function destroy($id)
    {
        $topic = FcmTopic::findOrFail($id);
        $topic->delete();

        return redirect()->route('advanced-notifications.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
    public function subscribe(Request $request, $id)
    {
        $request->validate(['token' => 'required|string']);
        $topic = FcmTopic::findOrFail($id);
        
        app(\AdvancedNotifications\Services\FirebaseService::class)->subscribeToTopic($request->token, $topic->name);
        
        return response()->json(['success' => true, 'message' => 'Subscribed to topic successfully']);
    }

    public function unsubscribe(Request $request, $id)
    {
        $request->validate(['token' => 'required|string']);
        $topic = FcmTopic::findOrFail($id);
        
        app(\AdvancedNotifications\Services\FirebaseService::class)->unsubscribeFromTopic($request->token, $topic->name);
        
        return response()->json(['success' => true, 'message' => 'Unsubscribed from topic successfully']);
    }
}
