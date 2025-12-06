<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use AdvancedNotifications\Models\NotificationAnalytic;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Aggregate stats by event type
        $stats = NotificationAnalytic::select('event_type', DB::raw('count(*) as total'))
            ->groupBy('event_type')
            ->pluck('total', 'event_type')
            ->toArray();

        $totalSent = $stats['sent'] ?? 0;
        $totalDelivered = $stats['delivered'] ?? 0;
        $totalRead = $stats['read'] ?? 0;
        $totalClicked = $stats['clicked'] ?? 0;

        // Channel breakdown
        $channelStats = NotificationAnalytic::select('channel', DB::raw('count(*) as total'))
            ->where('event_type', 'sent')
            ->groupBy('channel')
            ->pluck('total', 'channel')
            ->toArray();

        // Recent Activity
        $recentActivity = NotificationAnalytic::with('notification')
            ->latest()
            ->limit(10)
            ->get();

        return view('advanced-notifications::dashboard.analytics.index', compact(
            'totalSent', 'totalDelivered', 'totalRead', 'totalClicked', 
            'channelStats', 'recentActivity'
        ));
    }
}
