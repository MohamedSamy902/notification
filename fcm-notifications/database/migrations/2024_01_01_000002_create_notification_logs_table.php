<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration لجدول notification_logs
 * 
 * هذا الجدول يحفظ سجل الإشعارات المرسلة للمستخدمين
 * مفيد للتتبع والإحصائيات ومعرفة من قرأ الإشعار
 */
return new class extends Migration
{
    /**
     * تشغيل الـ Migration
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            
            // معرف المستخدم (Foreign Key)
            // nullable() لأن بعض الإشعارات قد تكون لـ Topics وليس لمستخدم محدد
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // عنوان الإشعار
            $table->string('title');
            
            // محتوى الإشعار
            $table->text('body');
            
            // بيانات إضافية (JSON)
            // يمكن أن تحتوي على: user_id, action, url, etc.
            $table->json('data')->nullable();
            
            // رابط الصورة (إذا كان الإشعار يحتوي على صورة)
            $table->string('image')->nullable();
            
            // الرابط الذي يفتح عند الضغط على الإشعار
            $table->string('link')->nullable();
            
            // نوع الإشعار (info, warning, success, error, custom)
            // يمكن استخدامه لتصنيف الإشعارات
            $table->enum('type', ['info', 'warning', 'success', 'error', 'custom'])->default('info');
            
            // هل تم قراءة الإشعار؟
            $table->boolean('is_read')->default(false);
            
            // تاريخ القراءة
            $table->timestamp('read_at')->nullable();
            
            // هل تم إرسال الإشعار بنجاح؟
            $table->boolean('is_sent')->default(true);
            
            // رسالة الخطأ (في حالة فشل الإرسال)
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // Indexes لتحسين الأداء
            $table->index('user_id');
            $table->index('is_read');
            $table->index('is_sent');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * التراجع عن الـ Migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_logs');
    }
};
