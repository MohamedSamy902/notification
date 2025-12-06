<?php

namespace AdvancedNotifications\Services;

use AdvancedNotifications\Contracts\ChannelInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FirebaseService implements ChannelInterface
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = config('advanced-notifications.firebase.project_id');
        
        if (!$this->projectId) {
            $credentialsPath = config('advanced-notifications.firebase.credentials_path');
            if (file_exists($credentialsPath)) {
                $json = json_decode(file_get_contents($credentialsPath), true);
                $this->projectId = $json['project_id'] ?? null;
            }
        }
    }

    // ... (keep other methods same, but I need to be careful not to delete them if I'm replacing the whole file or chunks)
    // The tool is replace_file_content, so I should target specific blocks.
    
    // Let's replace __construct first.
    
    // Then replace getAccessToken.
    
    // Wait, I can do it in one go if I include the whole file content or use multiple chunks?
    // replace_file_content allows replacing a chunk.
    
    // I will replace __construct first.
    
    // And then getAccessToken.
    
    // Actually, I'll use multi_replace_file_content if available? No, I have replace_file_content.
    // I'll do 2 calls or 1 call if I can match the range.
    // The methods are far apart.
    // I'll use 2 calls.
    
    // CALL 1: Constructor


    public function send($notifiable, $notification)
    {
        $tokens = $notifiable->routeNotificationFor('fcm');
        if (!$tokens) {
            return;
        }

        $tokens = is_array($tokens) ? $tokens : [$tokens];
        $payload = $notification->toFcm($notifiable);

        foreach ($tokens as $token) {
            $this->sendToToken($token, $payload['title'], $payload['body'], $payload['data'] ?? []);
        }
    }

    public function sendToToken($token, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $messagePayload = [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        if (!empty($data)) {
            // Ensure all data values are strings (FCM requirement)
            $formattedData = array_map(function($value) {
                return is_string($value) ? $value : (string)$value;
            }, $data);
            
            $messagePayload['data'] = (object)$formattedData;
        }

        $message = ['message' => $messagePayload];

        try {
            $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("FCM Send Error: " . $e->getMessage());
        }
    }

    public function subscribeToTopic($tokens, $topicName)
    {
        // 1. Sync with DB
        $topic = \AdvancedNotifications\Models\FcmTopic::firstOrCreate(['name' => $topicName]);
        
        $tokens = is_array($tokens) ? $tokens : [$tokens];
        
        foreach ($tokens as $token) {
            \AdvancedNotifications\Models\FcmTopicSubscription::firstOrCreate([
                'topic_id' => $topic->id,
                'token' => $token
            ]);
        }

        // 2. Call Firebase API
        // Using IID API with OAuth2 token as Server Key is deprecated
        $url = "https://iid.googleapis.com/iid/v1:batchAdd";
        $accessToken = $this->getAccessToken();
        
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'access_token_auth' => 'true', // Sometimes required for IID API with OAuth
        ];

        try {
            $this->client->post($url, [
                'headers' => $headers,
                'json' => [
                    'to' => "/topics/{$topicName}",
                    'registration_tokens' => $tokens,
                ],
            ]);
            Log::info("Subscribed " . count($tokens) . " tokens to topic: {$topicName}");
        } catch (\Exception $e) {
            Log::error("FCM Subscribe Error: " . $e->getMessage());
        }

        return true;
    }

    public function unsubscribeFromTopic($tokens, $topicName)
    {
        // 1. Sync with DB
        $topic = \AdvancedNotifications\Models\FcmTopic::where('name', $topicName)->first();
        
        if ($topic) {
            $tokens = is_array($tokens) ? $tokens : [$tokens];
            \AdvancedNotifications\Models\FcmTopicSubscription::where('topic_id', $topic->id)
                ->whereIn('token', $tokens)
                ->delete();
        }

        // 2. Call Firebase API
        $url = "https://iid.googleapis.com/iid/v1:batchRemove";
        $accessToken = $this->getAccessToken();

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'access_token_auth' => 'true',
        ];

        try {
            $this->client->post($url, [
                'headers' => $headers,
                'json' => [
                    'to' => "/topics/{$topicName}",
                    'registration_tokens' => $tokens,
                ],
            ]);
             Log::info("Unsubscribed " . count($tokens) . " tokens from topic: {$topicName}");
        } catch (\Exception $e) {
            Log::error("FCM Unsubscribe Error: " . $e->getMessage());
        }

        return true;
    }

    public function sendToTopic($topicName, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken();
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $messagePayload = [
            'topic' => $topicName,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];

        // Only add data if it's not empty, and ensure it's an object with string values
        if (!empty($data)) {
            $formattedData = array_map(function($value) {
                return is_string($value) ? $value : (string)$value;
            }, $data);
            
            $messagePayload['data'] = (object)$formattedData;
        }

        $message = ['message' => $messagePayload];

        try {
            $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $message,
            ]);
             Log::info("Sent FCM to Topic {$topicName}: {$title}");
        } catch (\Exception $e) {
            Log::error("FCM Topic Send Error: " . $e->getMessage());
        }
        return true;
    }

    protected function getAccessToken()
    {
        $credentialsPath = config('advanced-notifications.firebase.credentials_path');
        
        if (!file_exists($credentialsPath)) {
             Log::error("Firebase credentials file not found at: $credentialsPath");
             return null;
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        
        try {
            $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials($scopes, $credentialsPath);
            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to generate access token: " . $e->getMessage());
            return null;
        }
    }
}
