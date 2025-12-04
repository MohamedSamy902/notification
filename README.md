# FCM Notifications Package

## نظام إشعارات Firebase Cloud Messaging متكامل لـ Laravel

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-9%2B%7C10%2B%7C11%2B-red)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![Latest Version](https://img.shields.io/badge/version-1.1.0-orange)](https://github.com/MohamedSamy902/notification/releases)

هذا الـ Package يوفر نظام إشعارات متكامل باستخدام Firebase Cloud Messaging (FCM) مع دعم كامل للويب، Android، و iOS. تم بناؤه وفقاً لأفضل الممارسات ومبادئ **SOLID** لضمان كود نظيف وقابل للصيانة والتوسع.

---

## 📌 الإصدارات المتاحة

| الإصدار    | الوصف                          | الاستخدام           |
| ---------- | ------------------------------ | ------------------- |
| `dev-main` | آخر التحديثات (يتحدث تلقائياً) | للتطوير والتجربة ✅ |
| `v1.2.0`   | أحدث إصدار مستقر               | للإنتاج (موصى به)   |
| `v1.1.0`   | إصدار سابق                     | للتوافق مع كود قديم |
| `v1.0.0`   | الإصدار الأول                  | للمشاريع القديمة    |

---

## 📦 التثبيت

### الطريقة 1: تثبيت آخر التحديثات (dev-main) ✅

```bash
composer require mohamedsamy/fcm-notifications:dev-main
```

**أو** أضف إلى `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/MohamedSamy902/notification"
    }
  ],
  "require": {
    "mohamedsamy/fcm-notifications": "dev-main"
  }
}
```

### الطريقة 2: تثبيت إصدار مستقر

```bash
# آخر إصدار مستقر
composer require mohamedsamy/fcm-notifications:^1.2

# إصدار محدد
composer require mohamedsamy/fcm-notifications:v1.2.0
```

---

## ⚙️ الإعداد السريع

### 1. نشر الملفات

```bash
# نشر كل شيء مرة واحدة
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"

# أو نشر كل شيء على حدة:
php artisan vendor:publish --tag=fcm-notifications-config
php artisan vendor:publish --tag=fcm-notifications-migrations
php artisan vendor:publish --tag=fcm-notifications-views
php artisan vendor:publish --tag=fcm-notifications-assets
```

### 2. تشغيل Migrations

```bash
php artisan migrate
```

### 3. إعداد Firebase

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. أنشئ مشروع جديد أو اختر مشروع موجود
3. احصل على Service Account Key من Project Settings → Service Accounts
4. احفظ الملف في `storage/app/firebase_credentials.json`

### 4. إعداد متغيرات البيئة

أضف إلى `.env`:

```env
# مسار Firebase Credentials
FIREBASE_CREDENTIALS=app/firebase_credentials.json

# Firebase Configuration
FIREBASE_API_KEY=your_api_key
FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_STORAGE_BUCKET=your_project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your_sender_id
FIREBASE_APP_ID=your_app_id
FIREBASE_VAPID_KEY=your_vapid_key

# إعدادات العرض (اختياري)
FCM_DISPLAY_TYPE=both
```

---

## 📚 الوثائق الشاملة

### للمبتدئين - ابدأ من هنا! 🎯

- 🚀 **[دليل الإعداد الكامل من الصفر (عربي)](COMPLETE_SETUP_GUIDE_AR.md)** - **ابدأ من هنا!** دليل شامل من إنشاء التطبيق على Google حتى وصول الإشعار
- ✅ **[قائمة التحقق السريعة (عربي)](CHECKLIST_AR.md)** - خطوات سريعة مع checkboxes
- ⚡ **[مرجع الأوامر السريع (عربي)](COMMANDS_REFERENCE_AR.md)** - جميع الأوامر في مكان واحد

### للمطورين المتقدمين 💻

- 📖 **[دليل التثبيت والاستخدام الكامل (عربي)](INSTALLATION_GUIDE_AR.md)** - دليل شامل خطوة بخطوة
- 🏗️ **[شرح مبادئ SOLID المطبقة (عربي)](SOLID_PRINCIPLES_AR.md)** - فهم البنية المعمارية
- 📊 **[حالة الـ Package (عربي)](PACKAGE_STATUS_AR.md)** - تقرير شامل عن اكتمال الـ Package
- 🚀 **[دليل البدء السريع](QUICK_START.md)** - ابدأ في 5 دقائق
- 📝 **[سجل التغييرات](CHANGELOG.md)** - تتبع التحديثات

---

## ✨ المميزات

### الوظائف الأساسية

- ✅ إرسال إشعارات لمستخدم محدد (Single User)
- ✅ إرسال إشعارات لتوكن محدد (Single Token)
- ✅ إرسال إشعارات لـ Topic (مجموعة مستخدمين)
- ✅ الاشتراك في Topics
- ✅ دعم الصور في الإشعارات
- ✅ دعم الروابط (Click Actions)
- ✅ دعم الأصوات المخصصة
- ✅ دعم البيانات الإضافية (Custom Data)
- ✅ تسجيل الإشعارات في قاعدة البيانات (اختياري)
- ✅ دعم البيئات المختلفة (Local/Production)
- ✅ دعم Sweet Alert 2 لعرض الإشعارات

### جودة الكود

- ✅ **Clean Code**: كود نظيف وسهل القراءة
- ✅ **SOLID Principles**: تطبيق كامل لمبادئ SOLID الخمسة
- ✅ **Dependency Injection**: استخدام Interfaces بالكامل
- ✅ **Type Safety**: PHP 8+ Type Hints في كل مكان
- ✅ **No Dead Code**: لا يوجد كود غير مستخدم أو معلق عليه
- ✅ **Well Documented**: توثيق شامل بالعربية والإنجليزية
- ✅ **Testable**: سهل الاختبار باستخدام Mocks

---

## 🚀 الاستخدام السريع

### 1. إعداد Models

أنشئ Models المطلوبة:

```bash
php artisan make:model DeviceToken
php artisan make:model NotificationLog
```

**DeviceToken Model** (`app/Models/DeviceToken.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id', 'fcm_token', 'device_type',
        'device_name', 'last_used_at', 'is_active',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**NotificationLog Model** (`app/Models/NotificationLog.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id', 'title', 'body', 'data', 'image',
        'link', 'type', 'is_read', 'read_at', 'is_sent', 'error_message',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**تحديث User Model** (`app/Models/User.php`):

```php
public function devices()
{
    return $this->hasMany(DeviceToken::class);
}

public function notifications()
{
    return $this->hasMany(NotificationLog::class);
}
```

### 2. إعداد Frontend

في ملف Layout الرئيسي (مثل `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
</head>
<body>
    @yield('content')

    <!-- إضافة FCM Notifications -->
    @include('fcm-notifications::fcm-notifications')
</body>
</html>
```

### 3. إنشاء Service Worker

أنشئ ملف `public/firebase-messaging-sw.js`:

```javascript
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"
);

const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  authDomain: "YOUR_AUTH_DOMAIN",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_STORAGE_BUCKET",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
  appId: "YOUR_APP_ID",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon || "/favicon.ico",
    image: payload.notification.image,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
```

### 4. إنشاء API Endpoint

في `routes/api.php`:

```php
use App\Models\DeviceToken;
use Illuminate\Http\Request;

Route::post('/fcm-token', function (Request $request) {
    $request->validate([
        'token' => 'required|string',
        'device_type' => 'nullable|string',
        'device_name' => 'nullable|string',
    ]);

    if (auth()->check()) {
        DeviceToken::updateOrCreate(
            ['user_id' => auth()->id(), 'fcm_token' => $request->token],
            [
                'device_type' => $request->device_type ?? 'web',
                'device_name' => $request->device_name ?? 'Unknown',
                'last_used_at' => now(),
                'is_active' => true,
            ]
        );
    }

    return response()->json(['success' => true]);
});
```

### 5. إرسال الإشعارات

```php
use App\Packages\FcmNotifications\Contracts\NotificationServiceInterface;
use App\Models\User;

class NotificationController extends Controller
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // إرسال لمستخدم محدد
    public function sendToUser()
    {
        $user = User::find(1);

        $this->notificationService->sendToUser(
            $user,
            'مرحباً!',
            'هذا إشعار تجريبي',
            ['type' => 'success'],
            [
                'image' => 'https://example.com/image.png',
                'link' => 'https://example.com',
            ]
        );

        return response()->json(['message' => 'تم إرسال الإشعار']);
    }

    // إرسال لتوكن محدد
    public function sendToToken()
    {
        $this->notificationService->sendToToken(
            'FCM_TOKEN_HERE',
            'عنوان الإشعار',
            'محتوى الإشعار',
            ['type' => 'info']
        );

        return response()->json(['message' => 'تم الإرسال']);
    }

    // إرسال لـ Topic
    public function sendToTopic()
    {
        $this->notificationService->sendToTopic(
            'news',
            'خبر عاجل',
            'محتوى الخبر',
            ['type' => 'warning']
        );

        return response()->json(['message' => 'تم الإرسال']);
    }
}
```

---

## 🎨 التخصيص

### تخصيص Sweet Alert

في `config/fcm-notifications.php`:

```php
'display' => [
    'type' => env('FCM_DISPLAY_TYPE', 'both'),  // system, sweet_alert, both

    'sweet_alert' => [
        'enabled' => true,
        'position' => 'top-end',  // top, top-start, top-end, center, bottom
        'timer' => 5000,
        'toast' => true,
        'icon_type' => 'info',  // success, error, warning, info, question
    ],
],
```

---

## 🔧 استكشاف الأخطاء

### الإشعارات لا تصل

1. تحقق من Firebase Credentials:

```bash
cat storage/app/firebase_credentials.json
```

2. تحقق من الـ Logs:

```bash
tail -f storage/logs/laravel.log
```

3. تأكد من تفعيل Cloud Messaging API في Firebase Console

### Service Worker لا يعمل

1. تأكد من وجود الملف في `public/firebase-messaging-sw.js`
2. افتح Developer Tools → Application → Service Workers
3. تأكد من استخدام HTTPS أو localhost

---

## 📊 المتطلبات

- PHP 8.0 أو أحدث
- Laravel 9.x، 10.x، أو 11.x
- مشروع Firebase مع Cloud Messaging مفعّل
- Service Account Key من Firebase

---

## 🤝 المساهمة

المساهمات مرحب بها! يرجى قراءة [دليل المساهمة](CONTRIBUTING.md) للمزيد من المعلومات.

---

## 📄 الترخيص

هذا الـ Package مرخص تحت [MIT License](LICENSE).

---

## 🔗 الروابط المفيدة

- **GitHub Repository**: [https://github.com/MohamedSamy902/notification](https://github.com/MohamedSamy902/notification)
- **Packagist**: [https://packagist.org/packages/mohamedsamy/fcm-notifications](https://packagist.org/packages/mohamedsamy/fcm-notifications)
- **Firebase Console**: [https://console.firebase.google.com/](https://console.firebase.google.com/)
- **Firebase Documentation**: [https://firebase.google.com/docs/cloud-messaging](https://firebase.google.com/docs/cloud-messaging)

---

## 👨‍💻 المطور

**Mohamed Samy**

- Email: mohamedsamy9029@gmail.com
- GitHub: [@MohamedSamy902](https://github.com/MohamedSamy902)

---

## 🌟 إذا أعجبك الـ Package

إذا وجدت هذا الـ Package مفيداً، يرجى إعطاءه ⭐ على GitHub!

---

**الإصدار الحالي:** v1.2.0  
**آخر تحديث:** 2025-12-04  
**Branch التطوير:** `dev-main` ✅ (متاح للتثبيت)

---

## 💡 نصيحة

- **للتطوير:** استخدم `dev-main` للحصول على آخر التحديثات
- **للإنتاج:** استخدم `^1.2` للحصول على إصدار مستقر
- **للمزيد:** راجع [طرق التثبيت المتاحة](INSTALLATION_METHODS.md)
