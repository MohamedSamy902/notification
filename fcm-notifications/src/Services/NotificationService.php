<?php

namespace App\Packages\FcmNotifications\Services;

use App\Models\NotificationLog;
use App\Models\User;
use App\Packages\FcmNotifications\Contracts\NotificationServiceInterface;
use App\Packages\FcmNotifications\Contracts\FcmSenderInterface;
use App\Packages\FcmNotifications\Contracts\FcmAuthInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

/**
 * خدمة الإشعارات الرئيسية
 * 
 * مسؤولة عن:
 * - إرسال الإشعارات للمستخدمين والتوكنات والـ Topics
 * - حفظ سجل الإشعارات في قاعدة البيانات
 * - بناء هيكل رسائل FCM
 */
class NotificationService implements NotificationServiceInterface
{
    protected FcmSenderInterface $fcmSender;
    protected FcmAuthInterface $fcmAuthService;

    public function __construct(FcmSenderInterface $fcmSender, FcmAuthInterface $fcmAuthService)
    {
        $this->fcmSender = $fcmSender;
        $this->fcmAuthService = $fcmAuthService;
    }

    /**
     * الاشتراك في توبيك معين
     *
     * @param string $token توكن FCM
     * @param string $topic اسم التوبيك
     * @return bool
     */
    public function subscribeTokensToTopic(string $token, string $topic): bool
    {
        $token = trim($token);
        $topic = trim($topic);
        
        if (empty($token) || empty($topic)) {
            Log::warning('subscribeTokenToTopic called with empty token or topic');
            return false;
        }

        if (App::environment('local')) {
            $topic .= config('fcm-notifications.local_topic_suffix', '_dev');
        }

        return $this->fcmSender->subscribeTokensToTopic([$token], $topic);
    }

    /**
     * إرسال إشعار لتوكن معين
     *
     * @param string $token توكن FCM
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار (image, link, sound)
     * @return bool
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [], array $options = []): bool
    {
        $payload = $this->buildPayload($token, $title, $body, $data, $options);
        
        Log::info('Attempting to send FCM to Token', ['token' => $token]);
        
        $result = $this->fcmSender->send($payload);
        
        if ($result === true) {
            Log::info('FCM Sent Successfully To Token', ['token' => $token]);
            return true;
        }
        
        Log::error('FCM Failed To Send To Token', [
            'token' => $token,
            'error' => $result['error'] ?? 'Unknown error',
        ]);
        
        return false;
    }
    /**
     * إرسال إشعار لمستخدم معين وحفظه في قاعدة البيانات
     *
     * @param User $user المستخدم المستهدف
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار (image, link, sound)
     * @return void
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], array $options = []): void
    {
        if (config('fcm-notifications.log_to_database', true)) {
            NotificationLog::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'data' => json_encode($data),
                'image' => $options['image'] ?? null,
            ]);
        }

        $tokens = $user->devices()->pluck('fcm_token')->filter()->toArray();

        if (empty($tokens)) {
            Log::info("No FCM tokens found for user", ['user_id' => $user->id]);
            return;
        }

        foreach ($tokens as $token) {
            $payload = $this->buildPayload($token, $title, $body, $data, $options);
            
            Log::info('Attempting to send FCM to User Token', [
                'user_id' => $user->id,
                'token' => $token,
            ]);
            
            $result = $this->fcmSender->send($payload);
            
            if ($result === true) {
                Log::info('FCM Sent Successfully To User Token', [
                    'user_id' => $user->id,
                    'token' => $token,
                ]);
            } else {
                Log::error('FCM Failed To Send To User Token', [
                    'user_id' => $user->id,
                    'token' => $token,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        }
    }

    /**
     * إرسال إشعار لـ Topic (مجموعة مستخدمين)
     *
     * @param string $topic اسم التوبيك
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار (image, link, sound)
     * @return bool
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [], array $options = []): bool
    {
        if (App::environment('local')) {
            $topic .= config('fcm-notifications.local_topic_suffix', '_dev');
        }

        $payload = $this->buildPayload(null, $title, $body, $data, $options);
        unset($payload['token']);
        $payload['topic'] = $topic;

        $result = $this->fcmSender->send($payload);

        if ($result === true) {
            Log::info('FCM Topic Notification Sent Successfully', ['topic' => $topic]);
            return true;
        }
        
        Log::error('FCM Topic Notification Failed', [
            'topic' => $topic,
            'error' => $result['error'] ?? 'Unknown error',
        ]);
        
        return false;
    }

    /**
     * بناء هيكل رسالة FCM
     *
     * @param string|null $token توكن FCM أو null للـ Topic
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار
     * @return array
     */
    private function buildPayload(?string $token, string $title, string $body, array $data, array $options = []): array
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        if (!empty($options['image'])) {
            $notification['image'] = $options['image'];
        }

        $payload = [
            'notification' => $notification,
        ];

        if ($token) {
            $payload['token'] = $token;
        }

        $finalData = $data;
        if (!empty($options['link'])) {
            $finalData['link'] = $options['link'];
        }
        if (!empty($options['sound'])) {
            $finalData['sound'] = $options['sound'];
        }

        if (!empty($finalData)) {
            $payload['data'] = array_map('strval', $finalData);
        }

        $payload['android'] = [
            'priority' => config('fcm-notifications.priority', 'high'),
            'notification' => [
                'sound' => $options['sound'] ?? config('fcm-notifications.default_sound', 'default'),
                'click_action' => $options['link'] ?? null,
            ]
        ];

        $payload['webpush'] = [
            'headers' => [
                'Urgency' => 'high',
            ],
            'notification' => [
                'body' => $body,
                'requireInteraction' => config('fcm-notifications.display.system.require_interaction', true),
                'image' => $options['image'] ?? null,
            ],
            'fcm_options' => [
                'link' => $options['link'] ?? null,
            ]
        ];

        $payload['apns'] = [
            'payload' => [
                'aps' => [
                    'sound' => $options['sound'] ?? config('fcm-notifications.default_sound', 'default'),
                    'content-available' => 1,
                ],
            ],
        ];

        return $payload;
    }
}