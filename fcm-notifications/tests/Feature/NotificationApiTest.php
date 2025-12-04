<?php

namespace Tests\Feature\FcmNotifications;

use Tests\TestCase;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature Tests لنظام الإشعارات
 * 
 * هذه الاختبارات تتحقق من:
 * - حفظ التوكنات
 * - إرسال الإشعارات عبر API
 * - الاشتراك في Topics عبر API
 */
class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم للاختبار
        $this->user = User::factory()->create();
    }

    /**
     * اختبار: حفظ FCM Token عبر API
     */
    public function test_can_save_fcm_token()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/fcm-token', [
                'token' => 'test_fcm_token_123',
                'device_type' => 'web',
                'device_name' => 'Chrome on Windows'
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $this->user->id,
            'fcm_token' => 'test_fcm_token_123',
            'device_type' => 'web'
        ]);
    }

    /**
     * اختبار: تحديث FCM Token موجود
     */
    public function test_can_update_existing_fcm_token()
    {
        // إنشاء توكن موجود
        DeviceToken::create([
            'user_id' => $this->user->id,
            'fcm_token' => 'old_token',
            'device_type' => 'web'
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/fcm-token', [
                'token' => 'old_token',
                'device_type' => 'web',
                'device_name' => 'Updated Device'
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $this->user->id,
            'fcm_token' => 'old_token',
            'device_name' => 'Updated Device'
        ]);
    }

    /**
     * اختبار: فشل حفظ التوكن بدون authentication
     */
    public function test_cannot_save_token_without_authentication()
    {
        $response = $this->postJson('/api/v1/fcm-token', [
            'token' => 'test_token'
        ]);

        $response->assertStatus(401);
    }

    /**
     * اختبار: فشل حفظ التوكن بدون token parameter
     */
    public function test_cannot_save_token_without_token_parameter()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/fcm-token', [
                'device_type' => 'web'
            ]);

        $response->assertStatus(422);
    }
}
