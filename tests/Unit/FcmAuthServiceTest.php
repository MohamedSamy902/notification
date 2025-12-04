<?php

namespace Tests\Unit\FcmNotifications;

use Tests\TestCase;
use App\Packages\FcmNotifications\Services\Firebase\FcmAuthService;
use Illuminate\Support\Facades\Storage;

/**
 * Unit Tests لخدمة FcmAuthService
 * 
 * هذه الاختبارات تتحقق من:
 * - الحصول على Access Token بشكل صحيح
 * - الحصول على Project ID
 * - التعامل مع الأخطاء
 */
class FcmAuthServiceTest extends TestCase
{
    protected $fcmAuthService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fcmAuthService = new FcmAuthService();
    }

    /**
     * اختبار: التحقق من أن الخدمة يمكن إنشاؤها
     */
    public function test_service_can_be_instantiated()
    {
        $this->assertInstanceOf(FcmAuthService::class, $this->fcmAuthService);
    }

    /**
     * اختبار: الحصول على Access Token بنجاح
     * 
     * ملاحظة: هذا الاختبار يتطلب ملف credentials صحيح
     */
    public function test_can_get_access_token()
    {
        // تخطي الاختبار إذا لم يكن ملف الـ credentials موجود
        $credentialsPath = storage_path('app/firebase_credentials.json');
        if (!file_exists($credentialsPath)) {
            $this->markTestSkipped('Firebase credentials file not found');
        }

        $token = $this->fcmAuthService->getAccessToken();
        
        $this->assertNotNull($token);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    /**
     * اختبار: الحصول على Project ID بنجاح
     */
    public function test_can_get_project_id()
    {
        $credentialsPath = storage_path('app/firebase_credentials.json');
        if (!file_exists($credentialsPath)) {
            $this->markTestSkipped('Firebase credentials file not found');
        }

        $projectId = $this->fcmAuthService->getProjectId();
        
        $this->assertNotNull($projectId);
        $this->assertIsString($projectId);
        $this->assertNotEmpty($projectId);
    }

    /**
     * اختبار: التعامل مع ملف credentials غير موجود
     */
    public function test_returns_null_when_credentials_file_not_found()
    {
        // حفظ المسار الأصلي
        $originalPath = config('fcm-notifications.credentials_path');
        
        // تعيين مسار غير موجود
        config(['fcm-notifications.credentials_path' => 'non_existent_file.json']);
        
        $token = $this->fcmAuthService->getAccessToken();
        
        $this->assertNull($token);
        
        // استعادة المسار الأصلي
        config(['fcm-notifications.credentials_path' => $originalPath]);
    }
}
