<?php

namespace AdvancedNotifications\Services;

class ABTestingService
{
    public function determineVariant($campaign, $user)
    {
        // Simple 50/50 split based on user ID
        // In a real scenario, this could be more complex and stored in DB
        return ($user->id % 2 === 0) ? 'A' : 'B';
    }

    public function getTemplateForVariant($campaign, $variant)
    {
        // Assuming campaign has metadata defining templates for variants
        // $campaign->filters['ab_testing']['variants'][$variant]['template_id']
        
        $filters = $campaign->filters;
        if (isset($filters['ab_testing']['variants'][$variant]['template_id'])) {
            return $filters['ab_testing']['variants'][$variant]['template_id'];
        }
        
        return $campaign->template_id; // Fallback
    }
}
