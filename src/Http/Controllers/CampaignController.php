<?php

namespace AdvancedNotifications\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AdvancedNotifications\Services\CampaignService;

class CampaignController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'template_id' => 'required|exists:notification_templates,id',
            'filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date',
        ]);

        $campaign = $this->campaignService->createCampaign(
            $request->name,
            $request->template_id,
            $request->filters,
            $request->scheduled_at
        );

        return response()->json(['success' => true, 'campaign' => $campaign]);
    }

    public function index()
    {
        return \AdvancedNotifications\Models\NotificationCampaign::paginate(10);
    }
}
