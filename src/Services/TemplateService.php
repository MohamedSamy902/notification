<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Models\NotificationTemplate;

class TemplateService
{
    public function render($templateKey, $data = [], $locale = null)
    {
        $template = NotificationTemplate::where('key', $templateKey)->where('is_active', true)->first();

        if (!$template) {
            return null;
        }

        $locale = $locale ?? app()->getLocale();
        
        $title = $template->getTranslation('title', $locale);
        $body = $template->getTranslation('body', $locale);

        foreach ($data as $key => $value) {
            $title = str_replace('{' . $key . '}', $value, $title);
            $body = str_replace('{' . $key . '}', $value, $body);
        }

        return [
            'title' => $title,
            'body' => $body,
            'icon' => $template->icon,
            'action_url' => $template->action_url,
            'metadata' => $template->metadata,
        ];
    }
}
