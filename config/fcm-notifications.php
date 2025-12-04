<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials Path
    |--------------------------------------------------------------------------
    |
    | مسار ملف بيانات اعتماد Firebase (Service Account JSON)
    | يجب أن يكون المسار نسبياً من مجلد storage/app
    |
    | مثال: 'app/firebase_credentials.json'
    | سيشير إلى: storage/app/firebase_credentials.json
    |
    */
    'credentials_path' => env('FIREBASE_CREDENTIALS', 'app/firebase_credentials.json'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات Firebase للاتصال بالمشروع
    | يمكنك الحصول على هذه القيم من Firebase Console
    | Project Settings > General > Your apps > Web app
    |
    */
    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'), // من Cloud Messaging > Web Push certificates
    ],

    /*
    |--------------------------------------------------------------------------
    | Topic Suffix for Local Environment
    |--------------------------------------------------------------------------
    |
    | في بيئة التطوير المحلية، سيتم إضافة هذه اللاحقة تلقائياً لأسماء الـ Topics
    | لمنع إرسال إشعارات تجريبية للمستخدمين الحقيقيين
    |
    | مثال: إذا كان الـ Topic هو 'news' وأنت في بيئة local
    | سيصبح: 'news_dev'
    |
    */
    'local_topic_suffix' => '_dev',

    /*
    |--------------------------------------------------------------------------
    | Default Notification Sound
    |--------------------------------------------------------------------------
    |
    | الصوت الافتراضي للإشعارات
    | يمكن أن يكون 'default' أو اسم ملف صوت مخصص
    |
    */
    'default_sound' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Notification Priority
    |--------------------------------------------------------------------------
    |
    | أولوية الإشعارات الافتراضية
    | القيم المتاحة: 'high', 'normal'
    |
    | 'high' - للإشعارات المهمة التي يجب أن تظهر فوراً
    | 'normal' - للإشعارات العادية
    |
    */
    'priority' => 'high',

    /*
    |--------------------------------------------------------------------------
    | Log Notifications to Database
    |--------------------------------------------------------------------------
    |
    | هل تريد حفظ سجل الإشعارات في قاعدة البيانات؟
    | true - سيتم حفظ الإشعارات المرسلة للمستخدمين في جدول notification_logs
    | false - لن يتم الحفظ (مفيد لتوفير المساحة)
    |
    */
    'log_to_database' => true,

    /*
    |--------------------------------------------------------------------------
    | Device Token Model
    |--------------------------------------------------------------------------
    |
    | الـ Model المستخدم لحفظ توكنات الأجهزة
    | يمكنك استخدام Model مخصص إذا أردت
    |
    */
    'device_token_model' => \App\Models\DeviceToken::class,

    /*
    |--------------------------------------------------------------------------
    | Notification Log Model
    |--------------------------------------------------------------------------
    |
    | الـ Model المستخدم لحفظ سجل الإشعارات
    | يمكنك استخدام Model مخصص إذا أردت
    |
    */
    'notification_log_model' => \App\Models\NotificationLog::class,

    /*
    |--------------------------------------------------------------------------
    | FCM API Endpoints
    |--------------------------------------------------------------------------
    |
    | روابط API الخاصة بـ Firebase Cloud Messaging
    | لا تحتاج لتغيير هذه القيم إلا إذا تغيرت روابط Firebase
    |
    */
    'api' => [
        'fcm_send' => 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send',
        'iid_subscribe' => 'https://iid.googleapis.com/iid/v1:batchAdd',
        'oauth_token' => 'https://oauth2.googleapis.com/token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | إعدادات إعادة المحاولة في حالة فشل الإرسال
    |
    */
    'retry' => [
        'enabled' => true,           // تفعيل إعادة المحاولة
        'max_attempts' => 3,         // عدد المحاولات القصوى
        'delay_seconds' => 2,        // التأخير بين المحاولات (بالثواني)
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Display Options (Frontend)
    |--------------------------------------------------------------------------
    |
    | إعدادات عرض الإشعارات في المتصفح
    | يمكنك اختيار طريقة عرض الإشعارات عندما يكون التطبيق مفتوحاً
    |
    */
    'display' => [
        /*
        | نوع العرض المفضل:
        | 'system' - إشعارات النظام فقط (Firebase Native)
        | 'sweet_alert' - Sweet Alert فقط
        | 'both' - كلاهما معاً
        */
        'type' => env('FCM_DISPLAY_TYPE', 'both'),

        /*
        | إعدادات Sweet Alert
        */
        'sweet_alert' => [
            'enabled' => true,
            'position' => 'top-end',        // top, top-start, top-end, center, bottom, bottom-start, bottom-end
            'timer' => 5000,                // المدة بالميلي ثانية (5000 = 5 ثواني)
            'toast' => true,                // عرض كـ Toast (إشعار صغير)
            'show_confirm_button' => false, // إخفاء زر التأكيد
            'icon_type' => 'info',          // success, error, warning, info, question
            'show_close_button' => true,    // إظهار زر الإغلاق
            'allow_outside_click' => true,  // السماح بالإغلاق عند الضغط خارج الإشعار
        ],

        /*
        | إعدادات إشعارات النظام (System Notifications)
        */
        'system' => [
            'enabled' => true,
            'require_interaction' => true,  // إبقاء الإشعار ظاهراً حتى يتفاعل المستخدم
            'badge' => '/favicon.ico',      // أيقونة صغيرة تظهر في بعض الأنظمة
        ],
    ],
];

