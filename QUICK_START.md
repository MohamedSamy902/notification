# دليل الاستخدام السريع - FCM Notifications Package

## خطوات التثبيت السريعة

### 1. نسخ المجلد

```bash
cp -r packages/fcm-notifications /path/to/new/project/packages/
```

### 2. تحديث composer.json في المشروع الجديد

أضف هذا في قسم `repositories`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./packages/fcm-notifications"
    }
  ],
  "require": {
    "mohamedsamy/fcm-notifications": "*"
  }
}
```

### 3. تثبيت الـ Package

```bash
composer update
```

### 4. نشر الملفات

```bash
php artisan vendor:publish --tag=fcm-notifications-config
php artisan vendor:publish --tag=fcm-notifications-migrations
```

### 5. تشغيل Migrations

```bash
php artisan migrate
```

### 6. إعداد Firebase

1. ضع ملف `firebase_credentials.json` في `storage/app/`
2. أضف في `.env`:

```env
FIREBASE_CREDENTIALS=app/firebase_credentials.json
```

## أمثلة الاستخدام

### إرسال إشعار لمستخدم

```php
use App\Packages\FcmNotifications\Services\NotificationService;

$notificationService = app(NotificationService::class);

$notificationService->sendToUser(
    $user,
    'عنوان الإشعار',
    'محتوى الإشعار',
    ['key' => 'value'],  // بيانات إضافية (اختياري)
    [
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com',
        'sound' => 'default'
    ]
);
```

### إرسال إشعار لـ Topic

```php
$notificationService->sendToTopic(
    'news',
    'عنوان الإشعار',
    'محتوى الإشعار',
    [],
    [
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com'
    ]
);
```

### الاشتراك في Topic

```php
$notificationService->subscribeTokensToTopic(
    'FCM_TOKEN_HERE',
    'news'
);
```

## الملفات المطلوبة في المشروع الجديد

### 1. Model: DeviceToken

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token',
        'device_type',
        'device_name',
        'last_used_at',
        'is_active'
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

### 2. Model: NotificationLog

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'data',
        'image',
        'link',
        'type',
        'is_read',
        'read_at',
        'is_sent',
        'error_message'
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

### 3. إضافة Relationship في User Model

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

## Frontend Setup

### في HTML

```html
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
```

### تهيئة Firebase

```javascript
const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  projectId: "YOUR_PROJECT_ID",
  messagingSenderId: "YOUR_SENDER_ID",
  appId: "YOUR_APP_ID",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// طلب الإذن
Notification.requestPermission().then((permission) => {
  if (permission === "granted") {
    messaging.getToken({ vapidKey: "YOUR_VAPID_KEY" }).then((token) => {
      // إرسال التوكن للسيرفر
      saveTokenToServer(token);
    });
  }
});

// استقبال الإشعارات
messaging.onMessage(function (payload) {
  const notification = new Notification(payload.notification.title, {
    body: payload.notification.body,
    icon: payload.notification.icon || payload.notification.image,
    image: payload.notification.image,
  });

  notification.onclick = function () {
    window.open(payload.data.link || "/", "_blank");
  };
});
```

## الملاحظات المهمة

1. **الأمان**: لا تنسى إضافة `firebase_credentials.json` إلى `.gitignore`
2. **البيئات**: في بيئة `local` يتم إضافة `_dev` تلقائياً للـ Topics
3. **الصور**: يجب أن تكون روابط الصور مباشرة (HTTPS)
4. **Models**: تأكد من إنشاء Models في مشروعك الجديد

## الدعم

للمزيد من المعلومات، راجع ملف `README.md` الكامل.
