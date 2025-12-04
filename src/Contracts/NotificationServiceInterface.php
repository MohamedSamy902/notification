<?php

namespace App\Packages\FcmNotifications\Contracts;

use App\Models\User;

/**
 * واجهة خدمة الإشعارات الرئيسية
 * 
 * تحدد العقد (Contract) الذي يجب أن تلتزم به خدمة الإشعارات
 */
interface NotificationServiceInterface
{
    /**
     * الاشتراك في توبيك
     *
     * @param string $token توكن FCM
     * @param string $topic اسم التوبيك
     * @return bool
     */
    public function subscribeTokensToTopic(string $token, string $topic): bool;

    /**
     * إرسال إشعار لتوكن محدد
     *
     * @param string $token توكن FCM
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار
     * @return bool
     */
    public function sendToToken(string $token, string $title, string $body, array $data = [], array $options = []): bool;

    /**
     * إرسال إشعار لمستخدم محدد
     *
     * @param User $user المستخدم
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار
     * @return void
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], array $options = []): void;

    /**
     * إرسال إشعار لـ Topic
     *
     * @param string $topic اسم التوبيك
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية
     * @param array $options خيارات الإشعار
     * @return bool
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [], array $options = []): bool;
}
