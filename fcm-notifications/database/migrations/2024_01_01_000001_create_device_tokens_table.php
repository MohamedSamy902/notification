<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration لجدول device_tokens
 * 
 * هذا الجدول يحفظ توكنات FCM الخاصة بأجهزة المستخدمين
 * كل مستخدم يمكن أن يكون لديه عدة أجهزة (موبايل، تابلت، متصفح)
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
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            
            // معرف المستخدم (Foreign Key)
            // nullable() لأنه قد نحتاج لحفظ توكنات لمستخدمين غير مسجلين (Guest)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // توكن FCM الفريد للجهاز
            // unique() لضمان عدم تكرار نفس التوكن
            $table->string('fcm_token', 255)->unique();
            
            // نوع الجهاز (web, android, ios)
            // default('web') لأن معظم الاستخدام سيكون من المتصفح
            $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
            
            // اسم الجهاز (اختياري) - مثل: "Chrome on Windows", "iPhone 13"
            $table->string('device_name')->nullable();
            
            // آخر استخدام للتوكن
            // يمكن استخدامه لحذف التوكنات القديمة غير المستخدمة
            $table->timestamp('last_used_at')->nullable();
            
            // هل التوكن نشط؟
            // يمكن تعطيل التوكن بدلاً من حذفه
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes لتحسين الأداء
            $table->index('user_id');
            $table->index('device_type');
            $table->index('is_active');
        });
    }

    /**
     * التراجع عن الـ Migration
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_tokens');
    }
};
