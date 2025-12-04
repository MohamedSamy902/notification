# دليل التثبيت والاستخدام الشامل

## FCM Notifications Package

---

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [المتطلبات](#المتطلبات)
3. [التثبيت](#التثبيت)
4. [الإعداد](#الإعداد)
5. [الاستخدام](#الاستخدام)
6. [الميزات المتقدمة](#الميزات-المتقدمة)
7. [مبادئ SOLID المطبقة](#مبادئ-solid-المطبقة)
8. [استكشاف الأخطاء](#استكشاف-الأخطاء)
9. [الأسئلة الشائعة](#الأسئلة-الشائعة)

---

## 🎯 نظرة عامة

هذا الـ Package عبارة عن نظام إشعارات متكامل باستخدام **Firebase Cloud Messaging (FCM)** مصمم خصيصاً لـ Laravel. تم بناؤه وفقاً لأفضل الممارسات ومبادئ **SOLID** لضمان كود نظيف وقابل للصيانة والتوسع.

### ✨ المميزات الرئيسية

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
- ✅ كود نظيف يتبع مبادئ SOLID
- ✅ Dependency Injection كامل
- ✅ Interfaces لسهولة الاختبار والتوسع

---

## 📦 المتطلبات

### المتطلبات الأساسية

| المتطلب     | الإصدار المطلوب           |
| ----------- | ------------------------- |
| PHP         | 8.0 أو أحدث               |
| Laravel     | 9.x، 10.x، أو 11.x        |
| Composer    | 2.x                       |
| Guzzle HTTP | 7.x (يتم تثبيته تلقائياً) |

### متطلبات Firebase

1. **مشروع Firebase**: يجب أن يكون لديك مشروع على [Firebase Console](https://console.firebase.google.com/)
2. **Cloud Messaging مفعّل**: تأكد من تفعيل Firebase Cloud Messaging في مشروعك
3. **Service Account Key**: ملف JSON يحتوي على بيانات الاعتماد

---

## 🚀 التثبيت

### الخطوة 1: نسخ الـ Package

انسخ مجلد `fcm-notifications` إلى مجلد `app/Packages` في مشروع Laravel الخاص بك:

```bash
mkdir -p app/Packages
cp -r /path/to/fcm-notifications app/Packages/
```

### الخطوة 2: تحديث Composer Autoload

أضف الـ namespace إلى ملف `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "App\\Packages\\FcmNotifications\\": "app/Packages/fcm-notifications/src/"
    }
  }
}
```

ثم قم بتشغيل:

```bash
composer dump-autoload
```

### الخطوة 3: تسجيل Service Provider

في ملف `config/app.php`، أضف الـ Provider:

```php
'providers' => [
    // ...
    App\Packages\FcmNotifications\FcmNotificationServiceProvider::class,
],
```

**ملاحظة**: في Laravel 11+، يتم التسجيل تلقائياً عبر `bootstrap/providers.php`.

### الخطوة 4: نشر الملفات

قم بنشر ملفات الإعدادات والـ Migrations:

```bash
# نشر ملف الإعدادات
php artisan vendor:publish --tag=fcm-notifications-config

# نشر الـ Migrations
php artisan vendor:publish --tag=fcm-notifications-migrations

# نشر الـ Views (اختياري)
php artisan vendor:publish --tag=fcm-notifications-views

# نشر الـ Assets (JavaScript)
php artisan vendor:publish --tag=fcm-notifications-assets

# أو نشر كل شيء مرة واحدة
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"
```

### الخطوة 5: تشغيل Migrations

```bash
php artisan migrate
```

هذا سينشئ جدولين:

- `device_tokens`: لحفظ توكنات أجهزة المستخدمين
- `notification_logs`: لحفظ سجل الإشعارات المرسلة

---

## ⚙️ الإعداد

### 1. الحصول على Firebase Credentials

#### أ. إنشاء Service Account Key

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك
3. اذهب إلى **Project Settings** (⚙️)
4. اختر تبويب **Service Accounts**
5. اضغط على **Generate New Private Key**
6. احفظ الملف الذي تم تنزيله

#### ب. حفظ الملف

ضع الملف في مجلد `storage/app/`:

```bash
mv ~/Downloads/your-project-firebase-adminsdk-xxxxx.json storage/app/firebase_credentials.json
```

**⚠️ مهم جداً**: أضف هذا الملف إلى `.gitignore`:

```gitignore
storage/app/firebase_credentials.json
```

### 2. الحصول على Firebase Configuration

#### أ. Web App Configuration

1. في Firebase Console، اذهب إلى **Project Settings**
2. في تبويب **General**، انزل إلى **Your apps**
3. إذا لم يكن لديك Web App، اضغط على **Add app** واختر **Web**
4. انسخ القيم من `firebaseConfig`

#### ب. VAPID Key

1. في Firebase Console، اذهب إلى **Project Settings**
2. اختر تبويب **Cloud Messaging**
3. في قسم **Web Push certificates**، اضغط على **Generate key pair**
4. انسخ الـ Key

### 3. إعداد متغيرات البيئة (.env)

أضف المتغيرات التالية إلى ملف `.env`:

```env
# مسار ملف Firebase Credentials
FIREBASE_CREDENTIALS=app/firebase_credentials.json

# Firebase Web Configuration
FIREBASE_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789012
FIREBASE_APP_ID=1:123456789012:web:abcdef1234567890
FIREBASE_VAPID_KEY=BNxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# إعدادات العرض (اختياري)
FCM_DISPLAY_TYPE=both  # Options: system, sweet_alert, both
```

### 4. إضافة العلاقة في User Model

في ملف `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // ... الكود الموجود

    /**
     * علاقة المستخدم بأجهزته
     */
    public function devices()
    {
        return $this->hasMany(\App\Models\DeviceToken::class);
    }
}
```

### 5. إنشاء Models

#### أ. DeviceToken Model

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

#### ب. NotificationLog Model

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

---

## 💻 الاستخدام

### 1. إعداد Frontend

#### الطريقة الأولى: استخدام Blade Template الجاهز (موصى به)

في ملف Layout الرئيسي (مثل `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>
<body>
    <!-- المحتوى -->

    <!-- إضافة FCM Notifications -->
    @include('fcm-notifications::fcm-notifications')
</body>
</html>
```

هذا سيضيف تلقائياً:

- Firebase SDK
- Sweet Alert 2
- كل الإعدادات من ملف Config
- معالجة الإشعارات

#### الطريقة الثانية: التكامل اليدوي

إذا كنت تريد تخصيص أكثر، استخدم ملف JavaScript المنفصل:

```html
<!-- في ملف HTML -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
<script src="{{ asset('vendor/fcm-notifications/js/fcm-notifications.js') }}"></script>
```

### 2. إنشاء Service Worker

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
```

### 3. إنشاء API Endpoint لحفظ التوكنات

في `routes/api.php`:

```php
use Illuminate\Http\Request;
use App\Models\DeviceToken;

Route::middleware('auth:sanctum')->post('/fcm-token', function (Request $request) {
    $request->validate([
        'token' => 'required|string',
        'device_type' => 'nullable|string|in:web,android,ios',
        'device_name' => 'nullable|string',
    ]);

    $user = $request->user();

    DeviceToken::updateOrCreate(
        [
            'user_id' => $user->id,
            'fcm_token' => $request->token,
        ],
        [
            'device_type' => $request->device_type ?? 'web',
            'device_name' => $request->device_name ?? 'Unknown',
            'last_used_at' => now(),
            'is_active' => true,
        ]
    );

    return response()->json(['success' => true]);
});
```

### 4. إرسال الإشعارات من Backend

#### أ. إرسال لمستخدم محدد

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

    public function sendToUser()
    {
        $user = User::find(1);

        $this->notificationService->sendToUser(
            $user,
            'مرحباً بك!',
            'هذا إشعار تجريبي',
            ['user_id' => $user->id, 'type' => 'welcome'],
            [
                'image' => 'https://example.com/image.png',
                'link' => 'https://example.com/welcome',
                'sound' => 'default'
            ]
        );

        return response()->json(['message' => 'تم إرسال الإشعار']);
    }
}
```

#### ب. إرسال لتوكن محدد

```php
public function sendToToken()
{
    $token = 'fcm_token_here';

    $result = $this->notificationService->sendToToken(
        $token,
        'عنوان الإشعار',
        'محتوى الإشعار',
        ['key' => 'value'],
        ['image' => 'https://example.com/image.png']
    );

    return response()->json(['success' => $result]);
}
```

#### ج. إرسال لـ Topic

```php
public function sendToTopic()
{
    $result = $this->notificationService->sendToTopic(
        'news',  // اسم التوبيك
        'خبر عاجل',
        'تم إضافة خبر جديد',
        ['type' => 'news', 'news_id' => 123],
        [
            'image' => 'https://example.com/news.png',
            'link' => 'https://example.com/news/123'
        ]
    );

    return response()->json(['success' => $result]);
}
```

#### د. الاشتراك في Topic

```php
public function subscribeToTopic()
{
    $token = 'fcm_token_here';
    $topic = 'news';

    $result = $this->notificationService->subscribeTokensToTopic($token, $topic);

    return response()->json(['success' => $result]);
}
```

---

## 🎨 الميزات المتقدمة

### 1. تخصيص عرض الإشعارات

في ملف `config/fcm-notifications.php`:

```php
'display' => [
    'type' => env('FCM_DISPLAY_TYPE', 'both'),  // system, sweet_alert, both

    'sweet_alert' => [
        'enabled' => true,
        'position' => 'top-end',  // top, top-start, top-end, center, bottom, etc.
        'timer' => 5000,  // بالميلي ثانية
        'toast' => true,
        'show_confirm_button' => false,
        'icon_type' => 'info',  // success, error, warning, info, question
        'show_close_button' => true,
        'allow_outside_click' => true,
    ],

    'system' => [
        'enabled' => true,
        'require_interaction' => true,
        'badge' => '/favicon.ico',
    ],
],
```

### 2. تحديد نوع الأيقونة من Backend

```php
$this->notificationService->sendToUser(
    $user,
    'عملية ناجحة',
    'تم حفظ البيانات بنجاح',
    ['type' => 'success'],  // سيظهر أيقونة ✅
    ['link' => '/dashboard']
);
```

الأنواع المتاحة:

- `success` → ✅
- `error` → ❌
- `warning` → ⚠️
- `info` → ℹ️
- `question` → ❓

### 3. فصل البيئات (Local/Production)

في بيئة `local`، يتم إضافة `_dev` تلقائياً لأسماء Topics:

```php
// في Production
$topic = 'news';  // سيرسل إلى 'news'

// في Local
$topic = 'news';  // سيرسل إلى 'news_dev'
```

يمكنك تغيير اللاحقة في `config/fcm-notifications.php`:

```php
'local_topic_suffix' => '_dev',
```

### 4. تعطيل حفظ السجلات

إذا كنت لا تريد حفظ الإشعارات في قاعدة البيانات:

```php
'log_to_database' => false,
```

---

## 🏗️ مبادئ SOLID المطبقة

هذا الـ Package مبني وفقاً لمبادئ **SOLID** لضمان كود نظيف وقابل للصيانة:

### 1. Single Responsibility Principle (SRP)

كل Class له مسؤولية واحدة فقط:

- `FcmAuthService`: مسؤول فقط عن المصادقة مع Firebase
- `FcmSenderService`: مسؤول فقط عن إرسال الرسائل
- `NotificationService`: مسؤول عن تنسيق عملية الإرسال

### 2. Open/Closed Principle (OCP)

الكود مفتوح للتوسع ومغلق للتعديل:

```php
// يمكنك إنشاء Implementation جديد دون تعديل الكود الموجود
class CustomFcmSender implements FcmSenderInterface
{
    // تنفيذك الخاص
}
```

### 3. Liskov Substitution Principle (LSP)

يمكن استبدال أي Implementation بآخر دون كسر الكود:

```php
// في ServiceProvider
$this->app->singleton(FcmSenderInterface::class, CustomFcmSender::class);
```

### 4. Interface Segregation Principle (ISP)

Interfaces صغيرة ومحددة:

- `FcmAuthInterface`: فقط للمصادقة
- `FcmSenderInterface`: فقط للإرسال
- `NotificationServiceInterface`: للخدمة الرئيسية

### 5. Dependency Inversion Principle (DIP)

الاعتماد على Abstractions وليس Implementations:

```php
class NotificationService implements NotificationServiceInterface
{
    // يعتمد على Interface وليس Class محدد
    protected FcmSenderInterface $fcmSender;
    protected FcmAuthInterface $fcmAuthService;

    public function __construct(
        FcmSenderInterface $fcmSender,
        FcmAuthInterface $fcmAuthService
    ) {
        $this->fcmSender = $fcmSender;
        $this->fcmAuthService = $fcmAuthService;
    }
}
```

---

## 🔧 استكشاف الأخطاء

### المشكلة: الإشعارات لا تصل

**الحلول:**

1. تأكد من صحة Firebase Credentials:

```bash
cat storage/app/firebase_credentials.json
```

2. تحقق من الـ Logs:

```bash
tail -f storage/logs/laravel.log
```

3. تأكد من تفعيل Cloud Messaging في Firebase Console

4. تحقق من صلاحية التوكن:

```php
use App\Models\DeviceToken;

$tokens = DeviceToken::where('user_id', 1)->get();
dd($tokens);
```

### المشكلة: الصور لا تظهر

**الحلول:**

1. تأكد من أن الرابط مباشر وينتهي بـ `.png` أو `.jpg`
2. تأكد من أن الصورة متاحة عبر HTTPS
3. جرب صورة أصغر (بعض الأنظمة لا تدعم الصور الكبيرة)

### المشكلة: Service Worker لا يعمل

**الحلول:**

1. تأكد من أن الملف في `public/firebase-messaging-sw.js`
2. تأكد من أن الموقع يعمل على HTTPS (أو localhost)
3. افتح Developer Tools → Application → Service Workers وتحقق من التسجيل

### المشكلة: خطأ في Composer Autoload

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## ❓ الأسئلة الشائعة

### س: هل يمكن استخدام الـ Package مع تطبيقات الموبايل؟

**ج:** نعم! الـ Package يدعم:

- Web (PWA)
- Android (عبر FCM SDK)
- iOS (عبر APNs)

فقط احفظ التوكنات مع تحديد `device_type`.

### س: كيف أرسل إشعار لكل المستخدمين؟

**ج:** استخدم Topics:

```php
// اشترك كل المستخدمين في topic 'all_users'
$users = User::all();
foreach ($users as $user) {
    foreach ($user->devices as $device) {
        $this->notificationService->subscribeTokensToTopic(
            $device->fcm_token,
            'all_users'
        );
    }
}

// ثم أرسل للـ topic
$this->notificationService->sendToTopic('all_users', 'عنوان', 'محتوى');
```

### س: هل يمكن جدولة الإشعارات؟

**ج:** نعم، استخدم Laravel Scheduler أو Queue:

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

### س: كيف أتتبع من قرأ الإشعار؟

**ج:** استخدم جدول `notification_logs`:

```php
// عند قراءة الإشعار
$notification = NotificationLog::find($id);
$notification->update([
    'is_read' => true,
    'read_at' => now(),
]);
```

### س: هل الـ Package آمن؟

**ج:** نعم، بشرط:

1. عدم مشاركة `firebase_credentials.json`
2. إضافة الملف إلى `.gitignore`
3. استخدام HTTPS
4. تحديث الـ Dependencies بانتظام

---

## 📞 الدعم

للمساعدة أو الإبلاغ عن مشاكل:

1. راجع هذا الدليل أولاً
2. تحقق من الـ Logs في `storage/logs/laravel.log`
3. ابحث في Issues الموجودة
4. افتح Issue جديد مع تفاصيل المشكلة

---

## 📄 الترخيص

MIT License - يمكنك استخدام الـ Package بحرية في مشاريعك الشخصية والتجارية.

---

## 🙏 شكر خاص

تم بناء هذا الـ Package باستخدام:

- [Laravel Framework](https://laravel.com/)
- [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging)
- [Sweet Alert 2](https://sweetalert2.github.io/)
- [Guzzle HTTP Client](https://docs.guzzlephp.org/)

---

**تم التحديث**: ديسمبر 2024
**الإصدار**: 1.0.0
