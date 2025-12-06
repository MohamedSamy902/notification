<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Models\FcmTopic;
use AdvancedNotifications\Models\FcmTopicSubscription;
use AdvancedNotifications\Facades\AdvancedNotifications;

class DashboardController extends Controller
{
    public function index()
    {
        return view('advanced-notifications::dashboard.index');
    }

    public function sendNotification(Request $request)
    {
        // Logic to send manual notification from dashboard
        // ...
        return back()->with('success', 'Notification sent!');
    }
}


