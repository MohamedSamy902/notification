# 📦 دليل التثبيت السريع - FCM Notifications Package

## التثبيت من Composer/GitHub

---

## 🚀 الطريقة 1: التثبيت عبر Composer (الأسهل)

### الخطوة 1: تثبيت الـ Package

```bash
composer require mohamedsamy/fcm-notifications:dev-main
```

**أو** لتثبيت إصدار محدد:

```bash
composer require mohamedsamy/fcm-notifications:v1.1.0
```

### الخطوة 2: نشر الملفات

```bash
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"
```

### الخطوة 3: تشغيل Migrations

```bash
php artisan migrate
```

### الخطوة 4: إعداد .env

أضف إلى ملف `.env`:

```env
FIREBASE_CREDENTIALS=app/firebase_credentials.json
FIREBASE_API_KEY=your_api_key
FIREBASE_AUTH_DOMAIN=your_project.firebaseapp.com
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_STORAGE_BUCKET=your_project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=your_sender_id
FIREBASE_APP_ID=your_app_id
FIREBASE_VAPID_KEY=your_vapid_key
FCM_DISPLAY_TYPE=both
```

### الخطوة 5: حفظ Firebase Credentials

```bash
# ضع ملف firebase_credentials.json في storage/app/
cp ~/Downloads/your-firebase-key.json storage/app/firebase_credentials.json
chmod 600 storage/app/firebase_credentials.json
```

✅ **انتهى!** الآن اتبع باقي الخطوات في [دليل الإعداد الكامل](COMPLETE_SETUP_GUIDE_AR.md)

---

## 📦 الطريقة 2: التثبيت من GitHub مباشرة

### الخطوة 1: تحديث composer.json

افتح `composer.json` وأضف:

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

### الخطوة 2: تثبيت الـ Package

```bash
composer update
```

### الخطوة 3: نشر الملفات

```bash
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"
```

### الخطوة 4: تشغيل Migrations

```bash
php artisan migrate
```

### الخطوة 5: إعداد Firebase

راجع [دليل الإعداد الكامل](COMPLETE_SETUP_GUIDE_AR.md) للحصول على تعليمات Firebase.

---

## 🔧 الإعداد بعد التثبيت

### 1. إنشاء Models

**DeviceToken Model** - أنشئ `app/Models/DeviceToken.php`:

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

**NotificationLog Model** - أنشئ `app/Models/NotificationLog.php`:

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

**تحديث User Model** - في `app/Models/User.php`:

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

### 2. إضافة Blade Template

في Layout الرئيسي (مثل `resources/views/layouts/app.blade.php`):

```blade
@include('fcm-notifications::fcm-notifications')
```

### 3. إنشاء Service Worker

أنشئ `public/firebase-messaging-sw.js`:

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

---

## ✅ التحقق من التثبيت

### 1. تشغيل السيرفر

```bash
php artisan serve
```

### 2. اختبار الإشعارات

```bash
php artisan tinker
```

```php
$service = app(\App\Packages\FcmNotifications\Contracts\NotificationServiceInterface::class);

// إرسال إشعار تجريبي
$service->sendToToken(
    'YOUR_FCM_TOKEN',
    'اختبار',
    'رسالة تجريبية',
    ['type' => 'success']
);
```

---

## 🔍 استكشاف الأخطاء

### المشكلة: "Class not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### المشكلة: "Migration not found"

```bash
php artisan vendor:publish --tag=fcm-notifications-migrations
php artisan migrate
```

### المشكلة: "Config not found"

```bash
php artisan vendor:publish --tag=fcm-notifications-config
php artisan config:clear
```

---

## 📚 الخطوات التالية

بعد التثبيت، راجع:

1. **[دليل الإعداد الكامل](COMPLETE_SETUP_GUIDE_AR.md)** - للحصول على تعليمات Firebase الكاملة
2. **[قائمة التحقق](CHECKLIST_AR.md)** - للتأكد من عدم نسيان أي خطوة
3. **[مرجع الأوامر](COMMANDS_REFERENCE_AR.md)** - لجميع الأوامر المفيدة
4. **[README](README.md)** - للوثائق الكاملة

---

## 🔗 الروابط

- **GitHub**: [https://github.com/MohamedSamy902/notification](https://github.com/MohamedSamy902/notification)
- **Packagist**: [https://packagist.org/packages/mohamedsamy/fcm-notifications](https://packagist.org/packages/mohamedsamy/fcm-notifications)

---

## 💡 نصيحة

للحصول على أفضل تجربة، اتبع [دليل الإعداد الكامل من الصفر](COMPLETE_SETUP_GUIDE_AR.md) الذي يشرح كل شيء من إنشاء مشروع Firebase حتى وصول الإشعار بنجاح.

---

**الإصدار:** v1.1.0
**آخر تحديث:** 2025-12-04
