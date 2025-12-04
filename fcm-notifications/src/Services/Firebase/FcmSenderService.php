<?php

namespace App\Packages\FcmNotifications\Services\Firebase;

use App\Packages\FcmNotifications\Contracts\FcmSenderInterface;
use App\Packages\FcmNotifications\Contracts\FcmAuthInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة إرسال الإشعارات عبر FCM
 * 
 * مسؤولة عن:
 * - إرسال الرسائل إلى FCM API
 * - الاشتراك في Topics
 */
class FcmSenderService implements FcmSenderInterface
{
    protected FcmAuthInterface $authService;

    public function __construct(FcmAuthInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * إرسال رسالة FCM
     *
     * @param array $messagePayload بيانات الرسالة
     * @return bool
     */
    public function send(array $messagePayload): bool
    {
        $accessToken = $this->authService->getAccessToken();
        $projectId = $this->authService->getProjectId();

        if (!$accessToken || !$projectId) {
            Log::error('FCM: Missing Access Token or Project ID');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, ['message' => $messagePayload]);

        if ($response->failed()) {
            Log::error('FCM Send Error', ['error' => $response->body()]);
            return false;
        }

        return true;
    }
    
    /**
     * تشترك مجموعة من التوكنات في توبيك معين باستخدام Instance ID API للعمليات المجمعة.
     *
     * @param array $tokens قائمة بتوكنات FCM
     * @param string $topic اسم التوبيك
     * @return bool
     */
    public function subscribeTokensToTopic(array $tokens, string $topic): bool
    {
        $accessToken = $this->authService->getAccessToken();
        
        if (!$accessToken) {
            Log::error('FCM Topic Subscription Error: Missing Access Token.');
            return false;
        }

        // نقطة النهاية لعمليات الاشتراك المجمعة في Instance ID API
        $url = 'https://iid.googleapis.com/iid/v1:batchAdd';
        
        $payload = [
            // يجب أن يبدأ التوبيك بـ /topics/
            'to' => "/topics/{$topic}",
            'registration_tokens' => $tokens,
        ];

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'access_token_auth' => 'true',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            // تتحقق IID API بنجاح (HTTP 200) حتى لو كان هناك فشل جزئي في الـ tokens
            $body = $response->json();
            
            // التحقق من وجود أخطاء جزئية في التوكنات
            if (isset($body['results'])) {
                $failed = array_filter($body['results'], fn($result) => isset($result['error']));
                if (!empty($failed)) {
                    Log::warning('FCM Topic Subscription had partial failures.', [
                        'topic' => $topic,
                        'failure_count' => count($failed),
                        'sample_error' => $failed[0]['error'] ?? 'Unknown Error'
                    ]);
                }
            }
            Log::info('FCM Topic Subscription Success', [
                'topic' => $topic,
                'tokens_count' => count($tokens)
            ]);
            return true;
        }

        Log::error('FCM Topic Subscription Error: ' . $response->body(), ['topic' => $topic]);
        return false;
    }
}