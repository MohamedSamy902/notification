<?php

namespace App\Packages\FcmNotifications\Contracts;

/**
 * واجهة خدمة إرسال الإشعارات عبر FCM
 * 
 * تحدد العقد (Contract) الذي يجب أن تلتزم به أي خدمة إرسال FCM
 */
interface FcmSenderInterface
{
    /**
     * إرسال رسالة FCM
     *
     * @param array $messagePayload بيانات الرسالة
     * @return bool
     */
    public function send(array $messagePayload): bool;

    /**
     * الاشتراك في Topic
     *
     * @param array $tokens قائمة التوكنات
     * @param string $topic اسم التوبيك
     * @return bool
     */
    public function subscribeTokensToTopic(array $tokens, string $topic): bool;
}
