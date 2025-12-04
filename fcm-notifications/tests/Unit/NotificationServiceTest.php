<?php

namespace Tests\Unit\FcmNotifications;

use Tests\TestCase;
use App\Packages\FcmNotifications\Services\NotificationService;
use App\Packages\FcmNotifications\Services\Firebase\FcmSenderService;
use App\Packages\FcmNotifications\Services\Firebase\FcmAuthService;
use App\Models\User;
use Mockery;

/**
 * Unit Tests لخدمة NotificationService
 * 
 * هذه الاختبارات تتحقق من:
 * - إرسال الإشعارات لمستخدم محدد
 * - إرسال الإشعارات لتوكن محدد
 * - إرسال الإشعارات لـ Topic
 * - الاشتراك في Topics
 */
class NotificationServiceTest extends TestCase
{
    protected $notificationService;
    protected $fcmSenderMock;
    protected $fcmAuthMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إنشاء Mock للخدمات
        $this->fcmSenderMock = Mockery::mock(FcmSenderService::class);
        $this->fcmAuthMock = Mockery::mock(FcmAuthService::class);
        
        $this->notificationService = new NotificationService(
            $this->fcmSenderMock,
            $this->fcmAuthMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * اختبار: التحقق من أن الخدمة يمكن إنشاؤها
     */
    public function test_service_can_be_instantiated()
    {
        $this->assertInstanceOf(NotificationService::class, $this->notificationService);
    }

    /**
     * اختبار: إرسال إشعار لتوكن محدد بنجاح
     */
    public function test_can_send_to_token()
    {
        $token = 'test_fcm_token';
        $title = 'Test Title';
        $body = 'Test Body';
        
        // توقع أن يتم استدعاء send مرة واحدة ويعيد true
        $this->fcmSenderMock
            ->shouldReceive('send')
            ->once()
            ->andReturn(true);
        
        $result = $this->notificationService->sendToToken($token, $title, $body);
        
        $this->assertTrue($result);
    }

    /**
     * اختبار: إرسال إشعار لتوكن مع خيارات إضافية
     */
    public function test_can_send_to_token_with_options()
    {
        $token = 'test_fcm_token';
        $title = 'Test Title';
        $body = 'Test Body';
        $data = ['key' => 'value'];
        $options = [
            'image' => 'https://example.com/image.png',
            'link' => 'https://example.com',
            'sound' => 'default'
        ];
        
        $this->fcmSenderMock
            ->shouldReceive('send')
            ->once()
            ->andReturn(true);
        
        $result = $this->notificationService->sendToToken($token, $title, $body, $data, $options);
        
        $this->assertTrue($result);
    }

    /**
     * اختبار: إرسال إشعار لـ Topic بنجاح
     */
    public function test_can_send_to_topic()
    {
        $topic = 'news';
        $title = 'Test Title';
        $body = 'Test Body';
        
        $this->fcmSenderMock
            ->shouldReceive('send')
            ->once()
            ->andReturn(true);
        
        $result = $this->notificationService->sendToTopic($topic, $title, $body);
        
        $this->assertTrue($result);
    }

    /**
     * اختبار: الاشتراك في Topic بنجاح
     */
    public function test_can_subscribe_to_topic()
    {
        $token = 'test_fcm_token';
        $topic = 'news';
        
        $this->fcmSenderMock
            ->shouldReceive('subscribeTokensToTopic')
            ->once()
            ->with([$token], Mockery::any())
            ->andReturn(true);
        
        $result = $this->notificationService->subscribeTokensToTopic($token, $topic);
        
        $this->assertTrue($result);
    }

    /**
     * اختبار: فشل الاشتراك في Topic مع توكن فارغ
     */
    public function test_subscribe_fails_with_empty_token()
    {
        $result = $this->notificationService->subscribeTokensToTopic('', 'news');
        
        $this->assertFalse($result);
    }

    /**
     * اختبار: فشل الاشتراك في Topic مع topic فارغ
     */
    public function test_subscribe_fails_with_empty_topic()
    {
        $result = $this->notificationService->subscribeTokensToTopic('test_token', '');
        
        $this->assertFalse($result);
    }

    /**
     * اختبار: إضافة _dev للـ Topic في بيئة local
     */
    public function test_adds_dev_suffix_in_local_environment()
    {
        // تعيين البيئة كـ local
        app()->detectEnvironment(function () {
            return 'local';
        });
        
        $topic = 'news';
        
        $this->fcmSenderMock
            ->shouldReceive('subscribeTokensToTopic')
            ->once()
            ->with(Mockery::any(), 'news_dev')
            ->andReturn(true);
        
        $this->notificationService->subscribeTokensToTopic('test_token', $topic);
    }
}
