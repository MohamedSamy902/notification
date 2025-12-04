<?php

namespace App\Packages\FcmNotifications\Contracts;

/**
 * واجهة خدمة المصادقة مع Firebase
 * 
 * تحدد العقد (Contract) الذي يجب أن تلتزم به أي خدمة مصادقة FCM
 * مما يسمح بسهولة استبدال التنفيذ (Dependency Inversion Principle)
 */
interface FcmAuthInterface
{
    /**
     * الحصول على Access Token من Firebase
     *
     * @return string|null
     */
    public function getAccessToken(): ?string;

    /**
     * الحصول على Project ID من Firebase
     *
     * @return string|null
     */
    public function getProjectId(): ?string;
}
