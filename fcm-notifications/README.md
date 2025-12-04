# FCM Notifications Package

## نظام إشعارات Firebase Cloud Messaging متكامل لـ Laravel

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-9%2B%7C10%2B%7C11%2B-red)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![SOLID Principles](https://img.shields.io/badge/SOLID-Principles-orange)](SOLID_PRINCIPLES_AR.md)
[![Clean Code](https://img.shields.io/badge/Clean-Code-brightgreen)](INSTALLATION_GUIDE_AR.md)

هذا الـ Package يوفر نظام إشعارات متكامل باستخدام Firebase Cloud Messaging (FCM) مع دعم كامل للويب، Android، و iOS. تم بناؤه وفقاً لأفضل الممارسات ومبادئ **SOLID** لضمان كود نظيف وقابل للصيانة والتوسع.

---

## 📚 الوثائق الشاملة

- 📖 **[دليل التثبيت والاستخدام الكامل (عربي)](INSTALLATION_GUIDE_AR.md)** - دليل شامل خطوة بخطوة
- 🏗️ **[شرح مبادئ SOLID المطبقة (عربي)](SOLID_PRINCIPLES_AR.md)** - فهم البنية المعمارية
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

## المتطلبات الأساسية (Required)

1. **Laravel**: 9.x أو أحدث
2. **PHP**: 8.0 أو أحدث
3. **Firebase Project**: مشروع Firebase مع تفعيل Cloud Messaging
4. **Service Account Key**: ملف JSON من Firebase Console

## التثبيت

### 1. نسخ المجلد

انسخ مجلد `packages/fcm-notifications` إلى مشروعك الجديد.

### 2. تسجيل Service Provider

في ملف `config/app.php`:

```php
'providers' => [
    // ...
    App\Packages\FcmNotifications\FcmNotificationServiceProvider::class,
],
```

### 3. نشر الملفات

```bash
php artisan vendor:publish --tag=fcm-notifications
```

هذا سينشر:

- Migration لجدول `notification_logs`
- Migration لجدول `device_tokens`
- ملف الإعدادات `config/fcm-notifications.php`

### 4. تشغيل Migrations

```bash
php artisan migrate
```

### 5. إعداد Firebase

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك أو أنشئ مشروع جديد
3. اذهب إلى Project Settings > Service Accounts
4. اضغط على "Generate New Private Key"
5. احفظ الملف في `storage/app/firebase_credentials.json`

### 6. إعداد متغيرات البيئة

في ملف `.env`:

```env
FIREBASE_CREDENTIALS=app/firebase_credentials.json
```

## الاستخدام

### 1. إرسال إشعار لمستخدم محدد

```php
use App\Packages\FcmNotifications\Services\NotificationService;

$notificationService = app(NotificationService::class);

$notificationService->sendToUser(
    $user,                    // Required: User Model
    'عنوان الإشعار',          // Required: Title
    'محتوى الإشعار',          // Required: Body
    ['key' => 'value'],       // Optional: Custom Data
    [                         // Optional: Options
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com',
        'sound' => 'default'
    ]
);
```

### 2. إرسال إشعار لتوكن محدد

```php
$notificationService->sendToToken(
    'FCM_TOKEN_HERE',         // Required: FCM Token
    'عنوان الإشعار',          // Required: Title
    'محتوى الإشعار',          // Required: Body
    ['key' => 'value'],       // Optional: Custom Data
    [                         // Optional: Options
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com',
        'sound' => 'default'
    ]
);
```

### 3. إرسال إشعار لـ Topic

```php
$notificationService->sendToTopic(
    'news',                   // Required: Topic Name
    'عنوان الإشعار',          // Required: Title
    'محتوى الإشعار',          // Required: Body
    ['key' => 'value'],       // Optional: Custom Data
    [                         // Optional: Options
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com',
        'sound' => 'default'
    ]
);
```

### 4. الاشتراك في Topic

```php
$notificationService->subscribeTokensToTopic(
    'FCM_TOKEN_HERE',         // Required: FCM Token
    'news'                    // Required: Topic Name
);
```

## إعداد الـ Frontend

### 1. إضافة Firebase SDK

في ملف HTML الرئيسي:

```html
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
```

### 2. استخدام Blade Template الجاهز (الطريقة الأسهل)

في ملف Layout الرئيسي (مثل `resources/views/layouts/app.blade.php`):

```blade
@include('fcm-notifications::fcm-notifications')
```

هذا سيضيف تلقائياً:

- ✅ Firebase SDK
- ✅ Sweet Alert 2
- ✅ كل الإعدادات من ملف الـ Config
- ✅ معالجة الإشعارات تلقائياً

### 3. الطريقة اليدوية (للتخصيص الكامل)

#### أ. إضافة Sweet Alert 2

```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

#### ب. تهيئة Firebase

```javascript
const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  projectId: "YOUR_PROJECT_ID",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
  appId: "YOUR_APP_ID",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();
```

#### ج. إعدادات العرض

```javascript
const notificationConfig = {
  displayType: "both", // 'system', 'sweet_alert', 'both'
  sweetAlert: {
    enabled: true,
    position: "top-end",
    timer: 5000,
    toast: true,
    showConfirmButton: false,
    iconType: "info",
    showCloseButton: true,
    allowOutsideClick: true,
  },
  system: {
    enabled: true,
    requireInteraction: true,
    badge: "/favicon.ico",
  },
};
```

#### د. طلب الإذن والحصول على التوكن

```javascript
Notification.requestPermission().then((permission) => {
  if (permission === "granted") {
    messaging.getToken({ vapidKey: "YOUR_VAPID_KEY" }).then((token) => {
      // إرسال التوكن للسيرفر
      saveTokenToServer(token);
    });
  }
});
```

#### هـ. استقبال الإشعارات

```javascript
messaging.onMessage(function (payload) {
  console.log("Message received. ", payload);

  switch (notificationConfig.displayType) {
    case "sweet_alert":
      showSweetAlertNotification(payload);
      break;
    case "system":
      showSystemNotification(payload);
      break;
    case "both":
      showSweetAlertNotification(payload);
      showSystemNotification(payload);
      break;
  }
});
```

## خيارات عرض الإشعارات

### أنواع العرض المتاحة

يمكنك التحكم في طريقة عرض الإشعارات من ملف الإعدادات أو من `.env`:

#### 1. Sweet Alert فقط

```env
FCM_DISPLAY_TYPE=sweet_alert
```

**المميزات:**

- ✅ تصميم جميل ومتحرك
- ✅ يظهر في أعلى الصفحة
- ✅ قابل للتخصيص بالكامل
- ✅ يدعم الصور
- ✅ يمكن النقر عليه لفتح الرابط

**الاستخدام المثالي:**

- عندما تريد تجربة مستخدم أفضل
- للإشعارات داخل التطبيق
- عندما يكون المستخدم في الصفحة

#### 2. إشعارات النظام فقط

```env
FCM_DISPLAY_TYPE=system
```

**المميزات:**

- ✅ إشعارات نظام التشغيل الأصلية
- ✅ تظهر حتى لو كان المتصفح في الخلفية
- ✅ صوت الإشعار الافتراضي
- ✅ تبقى في مركز الإشعارات

**الاستخدام المثالي:**

- للإشعارات المهمة
- عندما تريد لفت انتباه المستخدم
- للإشعارات التي يجب أن تبقى

#### 3. كلاهما معاً (الافتراضي)

```env
FCM_DISPLAY_TYPE=both
```

**المميزات:**

- ✅ أفضل ما في العالمين
- ✅ Sweet Alert للتفاعل الفوري
- ✅ إشعار النظام للرجوع إليه لاحقاً

**الاستخدام المثالي:**

- للإشعارات المهمة جداً
- عندما تريد ضمان رؤية المستخدم للإشعار

### تخصيص Sweet Alert

في ملف `config/fcm-notifications.php`:

```php
'display' => [
    'sweet_alert' => [
        // تفعيل/تعطيل Sweet Alert
        'enabled' => true,

        // موقع الإشعار
        // 'top', 'top-start', 'top-end', 'center', 'bottom', 'bottom-start', 'bottom-end'
        'position' => 'top-end',

        // مدة العرض بالميلي ثانية (5000 = 5 ثواني)
        'timer' => 5000,

        // عرض كـ Toast (إشعار صغير في الزاوية)
        'toast' => true,

        // إظهار زر التأكيد
        'show_confirm_button' => false,

        // نوع الأيقونة الافتراضي
        // 'success', 'error', 'warning', 'info', 'question'
        'icon_type' => 'info',

        // إظهار زر الإغلاق (X)
        'show_close_button' => true,

        // السماح بالإغلاق عند الضغط خارج الإشعار
        'allow_outside_click' => true,
    ],
],
```

### مواقع Sweet Alert

| الموقع         | الوصف       | الاستخدام المثالي     |
| -------------- | ----------- | --------------------- |
| `top`          | أعلى الوسط  | للإشعارات المهمة      |
| `top-start`    | أعلى اليسار | للغات LTR             |
| `top-end`      | أعلى اليمين | للغات RTL (الافتراضي) |
| `center`       | في الوسط    | للتنبيهات الحرجة      |
| `bottom`       | أسفل الوسط  | للإشعارات الثانوية    |
| `bottom-start` | أسفل اليسار | للرسائل البسيطة       |
| `bottom-end`   | أسفل اليمين | للتحديثات             |

### أنواع الأيقونات

| النوع      | الاستخدام        |
| ---------- | ---------------- |
| `success`  | ✅ نجاح العملية  |
| `error`    | ❌ خطأ أو فشل    |
| `warning`  | ⚠️ تحذير         |
| `info`     | ℹ️ معلومات عامة  |
| `question` | ❓ سؤال أو تأكيد |

### تخصيص نوع الأيقونة من الـ Backend

يمكنك تحديد نوع الأيقونة عند إرسال الإشعار:

```php
$notificationService->sendToTopic(
    'news',
    'عملية ناجحة',
    'تم حفظ البيانات بنجاح',
    ['type' => 'success'],  // هنا! 👈
    [
        'image' => 'https://example.com/image.png',
        'link' => 'https://example.com'
    ]
);
```

الأنواع المتاحة في `data['type']`:

- `success` - للنجاح
- `error` - للأخطاء
- `warning` - للتحذيرات
- `info` - للمعلومات (الافتراضي)

### أمثلة عملية

#### مثال 1: إشعار نجاح مع Sweet Alert

```php
$notificationService->sendToUser(
    $user,
    'تم التسجيل بنجاح',
    'مرحباً بك في منصتنا',
    ['type' => 'success'],
    [
        'image' => 'https://example.com/welcome.png',
        'link' => '/dashboard'
    ]
);
```

#### مثال 2: تحذير مهم

```php
$notificationService->sendToTopic(
    'admins',
    'تحذير أمني',
    'محاولة دخول غير مصرح بها',
    ['type' => 'warning'],
    [
        'link' => '/admin/security-logs'
    ]
);
```

#### مثال 3: خطأ يحتاج انتباه

```php
$notificationService->sendToToken(
    $fcmToken,
    'فشل الدفع',
    'لم نتمكن من إتمام عملية الدفع',
    ['type' => 'error'],
    [
        'link' => '/payment/retry'
    ]
);
```

## Service Worker (Background)

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
  projectId: "YOUR_PROJECT_ID",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
  appId: "YOUR_APP_ID",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(function (payload) {
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: payload.notification.icon || payload.notification.image,
    image: payload.notification.image,
    data: payload.data,
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});
```

## الخيارات المتاحة (Options)

| الخيار  | النوع  | الوصف               | مثال                            |
| ------- | ------ | ------------------- | ------------------------------- |
| `image` | string | رابط الصورة         | `https://example.com/image.png` |
| `link`  | string | رابط يفتح عند الضغط | `https://example.com/page`      |
| `sound` | string | اسم ملف الصوت       | `default` أو `custom.mp3`       |

## البيانات المخصصة (Custom Data)

يمكنك إرسال أي بيانات إضافية:

```php
$data = [
    'user_id' => 123,
    'action' => 'view_profile',
    'url' => '/profile/123'
];
```

## Topics

### فصل البيئات

في بيئة `local`، يتم إضافة `_dev` تلقائياً لاسم الـ Topic:

- Production: `news`
- Local: `news_dev`

### أمثلة Topics شائعة

- `all_users` - كل المستخدمين
- `admins` - المدراء
- `news` - الأخبار
- `offers` - العروض

## حفظ التوكنات

### إنشاء API Endpoint

```php
Route::post('/api/fcm-token', function(Request $request) {
    $request->validate(['token' => 'required|string']);

    auth()->user()->devices()->updateOrCreate(
        ['fcm_token' => $request->token],
        [
            'device_type' => $request->device_type ?? 'web',
            'device_name' => $request->device_name ?? 'Unknown'
        ]
    );

    return response()->json(['success' => true]);
});
```

## الأمان

1. **لا تشارك** ملف `firebase_credentials.json`
2. **أضف** الملف إلى `.gitignore`
3. **استخدم** Environment Variables للإعدادات الحساسة

## استكشاف الأخطاء

### الإشعارات لا تصل

1. تأكد من صحة Firebase Credentials
2. تأكد من تفعيل Cloud Messaging في Firebase
3. تحقق من صلاحية التوكن
4. راجع الـ Logs في `storage/logs/laravel.log`

### الصور لا تظهر

1. تأكد من أن الرابط مباشر (ينتهي بـ .png, .jpg)
2. تأكد من أن الصورة متاحة عبر HTTPS
3. بعض أنظمة التشغيل لا تدعم الصور الكبيرة

### الروابط لا تعمل

1. تأكد من إضافة الرابط في `options['link']`
2. تأكد من معالجة الـ click event في JavaScript

## الدعم

للمساعدة أو الإبلاغ عن مشاكل، يرجى فتح Issue في المستودع.

## الترخيص

MIT License
