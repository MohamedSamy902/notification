# دليل التثبيت الكامل - من الصفر للنهاية 🚀

## FCM Notifications Package - Laravel

هذا الدليل يشرح **كل خطوة** من البداية حتى وصول الإشعار بنجاح، بدون أي خطوات ناقصة.

---

## 📋 جدول المحتويات

1. [إنشاء مشروع Firebase](#1-إنشاء-مشروع-firebase)
2. [تثبيت الـ Package في Laravel](#2-تثبيت-الـ-package-في-laravel)
3. [إعداد قاعدة البيانات](#3-إعداد-قاعدة-البيانات)
4. [إعداد Frontend](#4-إعداد-frontend)
5. [إرسال أول إشعار](#5-إرسال-أول-إشعار)
6. [اختبار النظام بالكامل](#6-اختبار-النظام-بالكامل)
7. [استكشاف الأخطاء](#7-استكشاف-الأخطاء)

---

## 1. إنشاء مشروع Firebase

### الخطوة 1.1: إنشاء المشروع

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اضغط على **"Add project"** أو **"إضافة مشروع"**
3. أدخل اسم المشروع (مثل: `my-app-notifications`)
4. اختر إذا كنت تريد Google Analytics (اختياري)
5. اضغط **"Create project"**

### الخطوة 1.2: الحصول على Service Account Key

1. في Firebase Console، اضغط على أيقونة الترس ⚙️ بجانب "Project Overview"
2. اختر **"Project settings"**
3. اذهب إلى تبويب **"Service accounts"**
4. اضغط على **"Generate new private key"**
5. اضغط **"Generate key"** في النافذة المنبثقة
6. سيتم تنزيل ملف JSON - **احتفظ به في مكان آمن**

### الخطوة 1.3: الحصول على Firebase Configuration

1. في **"Project settings"**، ابق في تبويب **"General"**
2. انزل إلى قسم **"Your apps"**
3. إذا لم يكن لديك Web App:
   - اضغط على أيقونة **`</>`** (Web)
   - أدخل اسم التطبيق (مثل: `My Web App`)
   - **لا تحتاج** لتفعيل Firebase Hosting
   - اضغط **"Register app"**
4. انسخ القيم من `firebaseConfig`:
   ```javascript
   const firebaseConfig = {
     apiKey: "AIza...", // انسخ هذا
     authDomain: "...", // انسخ هذا
     projectId: "...", // انسخ هذا
     storageBucket: "...", // انسخ هذا
     messagingSenderId: "...", // انسخ هذا
     appId: "...", // انسخ هذا
   };
   ```

### الخطوة 1.4: الحصول على VAPID Key

1. في **"Project settings"**، اذهب إلى تبويب **"Cloud Messaging"**
2. انزل إلى قسم **"Web Push certificates"**
3. إذا لم يكن هناك Key:
   - اضغط **"Generate key pair"**
4. انسخ الـ **Key pair** (يبدأ بـ `B...`)

### الخطوة 1.5: تفعيل Cloud Messaging API

1. في **"Project settings"** → **"Cloud Messaging"**
2. إذا رأيت رسالة تطلب تفعيل **Cloud Messaging API**:
   - اضغط على الرابط المقدم
   - اضغط **"Enable"**
3. انتظر بضع دقائق حتى يتم التفعيل

---

## 2. تثبيت الـ Package في Laravel

### الخطوة 2.1: إنشاء مشروع Laravel جديد (إذا لم يكن موجوداً)

```bash
# إنشاء مشروع Laravel جديد
composer create-project laravel/laravel my-notification-app

# الدخول للمشروع
cd my-notification-app
```

### الخطوة 2.2: نسخ الـ Package

```bash
# إنشاء مجلد packages
mkdir -p app/Packages

# نسخ الـ package (غيّر المسار حسب موقع الـ package عندك)
cp -r /path/to/fcm-notifications app/Packages/
```

### الخطوة 2.3: تحديث Composer Autoload

افتح ملف `composer.json` وأضف:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/",
      "App\\Packages\\FcmNotifications\\": "app/Packages/fcm-notifications/src/"
    }
  }
}
```

ثم شغّل:

```bash
composer dump-autoload
```

### الخطوة 2.4: تسجيل Service Provider

**في Laravel 10 وما قبل:**

افتح `config/app.php` وأضف في `providers`:

```php
'providers' => [
    // ...
    App\Packages\FcmNotifications\FcmNotificationServiceProvider::class,
],
```

**في Laravel 11:**

افتح `bootstrap/providers.php` وأضف:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Packages\FcmNotifications\FcmNotificationServiceProvider::class,
];
```

### الخطوة 2.5: نشر ملفات الـ Package

```bash
# نشر كل شيء مرة واحدة
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"

# أو نشر كل شيء على حدة:
php artisan vendor:publish --tag=fcm-notifications-config
php artisan vendor:publish --tag=fcm-notifications-migrations
php artisan vendor:publish --tag=fcm-notifications-views
php artisan vendor:publish --tag=fcm-notifications-assets
```

### الخطوة 2.6: حفظ Firebase Credentials

```bash
# انسخ ملف JSON الذي حملته من Firebase إلى storage/app
cp ~/Downloads/your-firebase-key.json storage/app/firebase_credentials.json

# تأكد من الصلاحيات
chmod 600 storage/app/firebase_credentials.json
```

**⚠️ مهم جداً:** أضف الملف إلى `.gitignore`:

```bash
echo "storage/app/firebase_credentials.json" >> .gitignore
```

### الخطوة 2.7: إعداد ملف .env

افتح `.env` وأضف:

```env
# مسار Firebase Credentials
FIREBASE_CREDENTIALS=app/firebase_credentials.json

# Firebase Configuration (من الخطوة 1.3)
FIREBASE_API_KEY=AIzaSy...
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789012
FIREBASE_APP_ID=1:123456789012:web:abc...
FIREBASE_VAPID_KEY=BNx...

# إعدادات العرض (اختياري)
FCM_DISPLAY_TYPE=both
```

---

## 3. إعداد قاعدة البيانات

### الخطوة 3.1: إعداد قاعدة البيانات

في `.env`، تأكد من إعدادات قاعدة البيانات:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### الخطوة 3.2: إنشاء قاعدة البيانات

```bash
# إذا كنت تستخدم MySQL
mysql -u root -p
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### الخطوة 3.3: تشغيل Migrations

```bash
php artisan migrate
```

هذا سينشئ الجداول التالية:

- `users` (إذا لم تكن موجودة)
- `device_tokens` (لحفظ توكنات الأجهزة)
- `notification_logs` (لحفظ سجل الإشعارات)

### الخطوة 3.4: إنشاء Models

**أ. DeviceToken Model**

أنشئ ملف `app/Models/DeviceToken.php`:

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
        'is_active',
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

**ب. NotificationLog Model**

أنشئ ملف `app/Models/NotificationLog.php`:

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
        'error_message',
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

**ج. تحديث User Model**

افتح `app/Models/User.php` وأضف:

```php
/**
 * علاقة المستخدم بأجهزته
 */
public function devices()
{
    return $this->hasMany(DeviceToken::class);
}

/**
 * علاقة المستخدم بإشعاراته
 */
public function notifications()
{
    return $this->hasMany(NotificationLog::class);
}
```

---

## 4. إعداد Frontend

### الخطوة 4.1: إنشاء Service Worker

أنشئ ملف `public/firebase-messaging-sw.js`:

```javascript
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"
);
importScripts(
  "https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"
);

// ضع إعدادات Firebase هنا (من .env)
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
  console.log("Received background message ", payload);

  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon:
      payload.notification.icon || payload.notification.image || "/favicon.ico",
    image: payload.notification.image,
    data: payload.data,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

// معالجة النقر على الإشعار
self.addEventListener("notificationclick", function (event) {
  event.notification.close();

  const link = event.notification.data?.link || "/";
  event.waitUntil(clients.openWindow(link));
});
```

**⚠️ مهم:** استبدل `YOUR_API_KEY` وباقي القيم بالقيم الحقيقية من `.env`

### الخطوة 4.2: إنشاء API Endpoint لحفظ التوكنات

في `routes/api.php`:

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\DeviceToken;

Route::prefix('v1')->group(function () {
    Route::post('/fcm-token', function (Request $request) {
        $validated = $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:web,android,ios',
            'device_name' => 'nullable|string',
        ]);

        // إذا كان المستخدم مسجل دخول
        if (auth()->check()) {
            DeviceToken::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'fcm_token' => $validated['token'],
                ],
                [
                    'device_type' => $validated['device_type'] ?? 'web',
                    'device_name' => $validated['device_name'] ?? 'Unknown',
                    'last_used_at' => now(),
                    'is_active' => true,
                ]
            );

            return response()->json(['success' => true, 'message' => 'Token saved']);
        }

        // إذا لم يكن مسجل دخول، احفظ بدون user_id
        DeviceToken::updateOrCreate(
            ['fcm_token' => $validated['token']],
            [
                'device_type' => $validated['device_type'] ?? 'web',
                'device_name' => $validated['device_name'] ?? 'Unknown',
                'last_used_at' => now(),
                'is_active' => true,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Token saved (guest)']);
    });
});
```

### الخطوة 4.3: إضافة الـ Blade Template

**الطريقة الأولى (الأسهل):** استخدام Template الجاهز

في ملف Layout الرئيسي (مثل `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
    <!-- ... -->
</head>
<body>
    <!-- المحتوى -->
    @yield('content')

    <!-- إضافة FCM Notifications -->
    @include('fcm-notifications::fcm-notifications')
</body>
</html>
```

**الطريقة الثانية:** إنشاء صفحة تجريبية

أنشئ `resources/views/test-notification.blade.php`:

```blade
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار الإشعارات</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .token-display {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            word-break: break-all;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 اختبار نظام الإشعارات</h1>

        <div id="status" class="status info">
            جاري التحميل...
        </div>

        <h3>FCM Token:</h3>
        <div class="token-display" id="fcm-token-display">
            في انتظار الحصول على التوكن...
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <p>إذا ظهر التوكن أعلاه، فالنظام يعمل بنجاح! ✅</p>
        </div>
    </div>

    <!-- تضمين FCM Notifications -->
    @include('fcm-notifications::fcm-notifications')

    <script>
        // تحديث حالة الصفحة
        setTimeout(() => {
            const statusDiv = document.getElementById('status');
            const tokenDiv = document.getElementById('fcm-token-display');

            if (tokenDiv.innerText !== 'في انتظار الحصول على التوكن...') {
                statusDiv.className = 'status success';
                statusDiv.innerText = '✅ تم الحصول على التوكن بنجاح! النظام جاهز لاستقبال الإشعارات.';
            } else {
                statusDiv.className = 'status error';
                statusDiv.innerText = '❌ لم يتم الحصول على التوكن. تحقق من Console للأخطاء.';
            }
        }, 3000);
    </script>
</body>
</html>
```

### الخطوة 4.4: إنشاء Route للصفحة التجريبية

في `routes/web.php`:

```php
Route::get('/test-notification', function () {
    return view('test-notification');
});
```

---

## 5. إرسال أول إشعار

### الخطوة 5.1: إنشاء Controller للإشعارات

```bash
php artisan make:controller NotificationController
```

في `app/Http/Controllers/NotificationController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Packages\FcmNotifications\Contracts\NotificationServiceInterface;
use App\Models\User;
use App\Models\DeviceToken;

class NotificationController extends Controller
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * إرسال إشعار تجريبي لتوكن محدد
     */
    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $result = $this->notificationService->sendToToken(
            $request->token,
            '🎉 مرحباً!',
            'هذا إشعار تجريبي من نظام الإشعارات',
            ['type' => 'success'],
            [
                'image' => 'https://via.placeholder.com/400x200/4CAF50/ffffff?text=Test+Notification',
                'link' => url('/'),
            ]
        );

        return response()->json([
            'success' => $result,
            'message' => $result ? 'تم إرسال الإشعار بنجاح!' : 'فشل إرسال الإشعار'
        ]);
    }

    /**
     * إرسال إشعار لمستخدم محدد
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        $this->notificationService->sendToUser(
            $user,
            '📢 إشعار جديد',
            'لديك رسالة جديدة في النظام',
            ['type' => 'info', 'user_id' => $user->id],
            [
                'image' => 'https://via.placeholder.com/400x200/2196F3/ffffff?text=New+Message',
                'link' => url('/messages'),
            ]
        );

        return response()->json(['message' => 'تم إرسال الإشعار']);
    }

    /**
     * إرسال إشعار لـ Topic
     */
    public function sendToTopic(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
        ]);

        $result = $this->notificationService->sendToTopic(
            $request->topic,
            '📰 خبر عاجل',
            'تم إضافة محتوى جديد في النظام',
            ['type' => 'warning'],
            [
                'image' => 'https://via.placeholder.com/400x200/FF9800/ffffff?text=Breaking+News',
                'link' => url('/news'),
            ]
        );

        return response()->json([
            'success' => $result,
            'message' => $result ? 'تم إرسال الإشعار للتوبيك' : 'فشل الإرسال'
        ]);
    }

    /**
     * الاشتراك في Topic
     */
    public function subscribeToTopic(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'topic' => 'required|string',
        ]);

        $result = $this->notificationService->subscribeTokensToTopic(
            $request->token,
            $request->topic
        );

        return response()->json([
            'success' => $result,
            'message' => $result ? 'تم الاشتراك في التوبيك' : 'فشل الاشتراك'
        ]);
    }

    /**
     * عرض كل التوكنات المحفوظة
     */
    public function listTokens()
    {
        $tokens = DeviceToken::with('user')
            ->where('is_active', true)
            ->latest()
            ->get();

        return response()->json($tokens);
    }
}
```

### الخطوة 5.2: إضافة Routes

في `routes/web.php`:

```php
use App\Http\Controllers\NotificationController;

Route::prefix('notifications')->group(function () {
    Route::post('/send-test', [NotificationController::class, 'sendTestNotification']);
    Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
    Route::post('/send-to-topic', [NotificationController::class, 'sendToTopic']);
    Route::post('/subscribe-to-topic', [NotificationController::class, 'subscribeToTopic']);
    Route::get('/list-tokens', [NotificationController::class, 'listTokens']);
});
```

---

## 6. اختبار النظام بالكامل

### الخطوة 6.1: تشغيل السيرفر

```bash
php artisan serve
```

### الخطوة 6.2: فتح صفحة الاختبار

1. افتح المتصفح واذهب إلى: `http://localhost:8000/test-notification`
2. سيطلب منك المتصفح إذن الإشعارات - اضغط **"Allow"** أو **"السماح"**
3. بعد ثوانٍ، يجب أن يظهر FCM Token في الصفحة

### الخطوة 6.3: الحصول على التوكن

1. انسخ التوكن من الصفحة
2. أو افتح Developer Console (F12) وشاهد الـ Logs

### الخطوة 6.4: إرسال إشعار تجريبي

**باستخدام Postman أو cURL:**

```bash
curl -X POST http://localhost:8000/notifications/send-test \
  -H "Content-Type: application/json" \
  -d '{"token": "YOUR_FCM_TOKEN_HERE"}'
```

**باستخدام Tinker:**

```bash
php artisan tinker
```

```php
$service = app(\App\Packages\FcmNotifications\Contracts\NotificationServiceInterface::class);

$service->sendToToken(
    'YOUR_FCM_TOKEN_HERE',
    'اختبار',
    'هذا إشعار تجريبي',
    ['type' => 'success']
);
```

### الخطوة 6.5: التحقق من النتيجة

يجب أن ترى:

1. ✅ Sweet Alert يظهر في أعلى الصفحة
2. ✅ إشعار النظام (System Notification)
3. ✅ في Console: "Message received"

---

## 7. استكشاف الأخطاء

### المشكلة: "Permission denied" عند طلب الإذن

**الحل:**

- تأكد أنك تستخدم HTTPS أو localhost
- جرب متصفح آخر (Chrome موصى به)
- امسح الـ Cache والـ Cookies

### المشكلة: "Failed to get token"

**الحل:**

1. تحقق من VAPID Key في `.env`
2. تأكد من تفعيل Cloud Messaging API في Firebase
3. تحقق من Service Worker:
   ```javascript
   // في Console
   navigator.serviceWorker.getRegistrations().then((registrations) => {
     console.log(registrations);
   });
   ```

### المشكلة: الإشعار لا يصل

**الحل:**

1. تحقق من Logs:

   ```bash
   tail -f storage/logs/laravel.log
   ```

2. تحقق من Firebase Credentials:

   ```bash
   cat storage/app/firebase_credentials.json
   ```

3. تحقق من صلاحية التوكن:
   ```php
   php artisan tinker
   DeviceToken::all();
   ```

### المشكلة: "Invalid token"

**الحل:**

- التوكن قد يكون منتهي الصلاحية
- احذف التوكن القديم من قاعدة البيانات
- أعد تحميل الصفحة للحصول على توكن جديد

### المشكلة: Service Worker لا يعمل

**الحل:**

1. تأكد من وجود الملف في `public/firebase-messaging-sw.js`
2. افتح: `http://localhost:8000/firebase-messaging-sw.js` - يجب أن يظهر الكود
3. في Developer Tools → Application → Service Workers
   - يجب أن ترى Service Worker مسجل
   - إذا كان هناك خطأ، اضغط "Unregister" ثم أعد تحميل الصفحة

### المشكلة: الصور لا تظهر

**الحل:**

- استخدم روابط HTTPS فقط
- تأكد أن الرابط ينتهي بـ `.png` أو `.jpg`
- جرب صورة أصغر (بعض الأنظمة لا تدعم الصور الكبيرة)

---

## 8. الخطوات التالية

### إرسال إشعارات لكل المستخدمين

```php
// اشترك كل المستخدمين في topic 'all_users'
$users = User::all();
foreach ($users as $user) {
    foreach ($user->devices as $device) {
        $service->subscribeTokensToTopic($device->fcm_token, 'all_users');
    }
}

// ثم أرسل للـ topic
$service->sendToTopic('all_users', 'إعلان', 'رسالة لكل المستخدمين');
```

### جدولة الإشعارات

```php
// في Job
class SendScheduledNotification implements ShouldQueue
{
    public function handle(NotificationServiceInterface $service)
    {
        $service->sendToTopic('news', 'خبر مجدول', 'المحتوى');
    }
}

// جدولة
SendScheduledNotification::dispatch()->delay(now()->addHours(2));
```

### تتبع قراءة الإشعارات

```php
// عند قراءة الإشعار
Route::post('/notifications/{id}/mark-read', function($id) {
    $notification = NotificationLog::find($id);
    $notification->update([
        'is_read' => true,
        'read_at' => now(),
    ]);

    return response()->json(['success' => true]);
});
```

---

## 9. قائمة التحقق النهائية ✅

قبل النشر في Production، تأكد من:

- [ ] ملف `firebase_credentials.json` في `.gitignore`
- [ ] جميع القيم في `.env` صحيحة
- [ ] Cloud Messaging API مفعّل في Firebase
- [ ] Service Worker يعمل بشكل صحيح
- [ ] الإشعارات تصل بنجاح في بيئة التطوير
- [ ] قاعدة البيانات تحفظ التوكنات والإشعارات
- [ ] HTTPS مفعّل في Production
- [ ] تم اختبار الإشعارات على أجهزة مختلفة

---

## 10. الدعم والمساعدة

إذا واجهت أي مشكلة:

1. راجع هذا الدليل بالكامل
2. تحقق من `storage/logs/laravel.log`
3. افتح Developer Console (F12) وشاهد الأخطاء
4. تأكد من جميع الخطوات تمت بشكل صحيح

---

## الخلاصة

الآن لديك نظام إشعارات متكامل يعمل بالكامل! 🎉

الـ Package يدعم:

- ✅ إرسال لمستخدم محدد
- ✅ إرسال لتوكن محدد
- ✅ إرسال لـ Topics
- ✅ Sweet Alert مخصص
- ✅ إشعارات النظام
- ✅ حفظ السجلات
- ✅ دعم الصور والروابط
- ✅ فصل بيئات Local/Production

**كل شيء جاهز ولا يوجد أي كود ناقص!** 🚀
