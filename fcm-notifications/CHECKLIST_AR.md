# ✅ قائمة التحقق السريعة - FCM Notifications Package

## من إنشاء التطبيق على Google حتى وصول الإشعار

---

## 📱 المرحلة 1: إعداد Firebase (15 دقيقة)

### 1.1 إنشاء المشروع

- [ ] اذهب إلى https://console.firebase.google.com/
- [ ] اضغط "Add project" أو "إضافة مشروع"
- [ ] أدخل اسم المشروع
- [ ] اضغط "Create project"

### 1.2 الحصول على Service Account Key

- [ ] اضغط على ⚙️ → Project Settings
- [ ] اذهب إلى تبويب "Service accounts"
- [ ] اضغط "Generate new private key"
- [ ] احفظ الملف JSON

### 1.3 الحصول على Web Configuration

- [ ] في Project Settings → تبويب "General"
- [ ] انزل إلى "Your apps"
- [ ] اضغط `</>` (Web) إذا لم يكن موجوداً
- [ ] انسخ جميع قيم `firebaseConfig`

### 1.4 الحصول على VAPID Key

- [ ] Project Settings → تبويب "Cloud Messaging"
- [ ] في "Web Push certificates"
- [ ] اضغط "Generate key pair"
- [ ] انسخ الـ Key

### 1.5 تفعيل Cloud Messaging API

- [ ] في Cloud Messaging، ابحث عن رسالة التفعيل
- [ ] اضغط على الرابط المقدم
- [ ] اضغط "Enable"

---

## 💻 المرحلة 2: تثبيت الـ Package (10 دقائق)

### 2.1 نسخ الـ Package

```bash
mkdir -p app/Packages
cp -r /path/to/fcm-notifications app/Packages/
```

- [ ] تم نسخ المجلد

### 2.2 تحديث Composer

في `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\Packages\\FcmNotifications\\": "app/Packages/fcm-notifications/src/"
    }
}
```

- [ ] تم تحديث composer.json
- [ ] تم تشغيل `composer dump-autoload`

### 2.3 تسجيل Service Provider

**Laravel 10 وما قبل:** في `config/app.php`
**Laravel 11:** في `bootstrap/providers.php`

```php
App\Packages\FcmNotifications\FcmNotificationServiceProvider::class,
```

- [ ] تم تسجيل Service Provider

### 2.4 نشر الملفات

```bash
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"
```

- [ ] تم نشر الملفات

### 2.5 حفظ Firebase Credentials

```bash
cp ~/Downloads/your-firebase-key.json storage/app/firebase_credentials.json
chmod 600 storage/app/firebase_credentials.json
echo "storage/app/firebase_credentials.json" >> .gitignore
```

- [ ] تم حفظ الملف
- [ ] تم ضبط الصلاحيات
- [ ] تم إضافته إلى .gitignore

### 2.6 تحديث .env

```env
FIREBASE_CREDENTIALS=app/firebase_credentials.json
FIREBASE_API_KEY=...
FIREBASE_AUTH_DOMAIN=...
FIREBASE_PROJECT_ID=...
FIREBASE_STORAGE_BUCKET=...
FIREBASE_MESSAGING_SENDER_ID=...
FIREBASE_APP_ID=...
FIREBASE_VAPID_KEY=...
FCM_DISPLAY_TYPE=both
```

- [ ] تم تحديث جميع القيم

---

## 🗄️ المرحلة 3: قاعدة البيانات (5 دقائق)

### 3.1 إعداد قاعدة البيانات

- [ ] تم ضبط إعدادات DB في .env
- [ ] تم إنشاء قاعدة البيانات

### 3.2 تشغيل Migrations

```bash
php artisan migrate
```

- [ ] تم إنشاء جدول device_tokens
- [ ] تم إنشاء جدول notification_logs

### 3.3 إنشاء Models

- [ ] تم إنشاء `app/Models/DeviceToken.php`
- [ ] تم إنشاء `app/Models/NotificationLog.php`
- [ ] تم تحديث `app/Models/User.php` (إضافة العلاقات)

---

## 🎨 المرحلة 4: Frontend (10 دقائق)

### 4.1 إنشاء Service Worker

- [ ] تم إنشاء `public/firebase-messaging-sw.js`
- [ ] تم استبدال القيم بالقيم الحقيقية من .env

### 4.2 إنشاء API Endpoint

في `routes/api.php`:

- [ ] تم إضافة endpoint `/api/v1/fcm-token`

### 4.3 إضافة Blade Template

في Layout الرئيسي:

```blade
@include('fcm-notifications::fcm-notifications')
```

- [ ] تم إضافة Template

### 4.4 إنشاء صفحة اختبار (اختياري)

- [ ] تم إنشاء `resources/views/test-notification.blade.php`
- [ ] تم إضافة Route `/test-notification`

---

## 🚀 المرحلة 5: الاختبار (5 دقائق)

### 5.1 تشغيل السيرفر

```bash
php artisan serve
```

- [ ] السيرفر يعمل

### 5.2 فتح صفحة الاختبار

- [ ] فتح `http://localhost:8000/test-notification`
- [ ] السماح بالإشعارات في المتصفح
- [ ] ظهور FCM Token في الصفحة

### 5.3 نسخ التوكن

- [ ] تم نسخ التوكن من الصفحة

### 5.4 إرسال إشعار تجريبي

```bash
php artisan tinker
```

```php
$service = app(\App\Packages\FcmNotifications\Contracts\NotificationServiceInterface::class);
$service->sendToToken('YOUR_TOKEN', 'اختبار', 'رسالة تجريبية', ['type' => 'success']);
```

- [ ] تم إرسال الإشعار

### 5.5 التحقق من الوصول

- [ ] ظهور Sweet Alert
- [ ] ظهور إشعار النظام
- [ ] ظهور "Message received" في Console

---

## 🔍 المرحلة 6: استكشاف الأخطاء (إذا لزم الأمر)

### إذا لم يظهر التوكن:

- [ ] تحقق من Console للأخطاء
- [ ] تحقق من VAPID Key في .env
- [ ] تحقق من Service Worker في DevTools

### إذا لم يصل الإشعار:

- [ ] تحقق من `storage/logs/laravel.log`
- [ ] تحقق من Firebase Credentials
- [ ] تحقق من تفعيل Cloud Messaging API

### إذا كان Service Worker لا يعمل:

- [ ] تحقق من وجود الملف في `public/`
- [ ] افتح DevTools → Application → Service Workers
- [ ] Unregister وأعد تحميل الصفحة

---

## ✅ قائمة التحقق النهائية

### الكود

- [ ] جميع الملفات موجودة
- [ ] Service Provider مسجل
- [ ] Migrations تمت بنجاح
- [ ] Models تم إنشاؤها

### Firebase

- [ ] Service Account Key محفوظ
- [ ] جميع القيم في .env صحيحة
- [ ] Cloud Messaging API مفعّل
- [ ] VAPID Key صحيح

### Frontend

- [ ] Service Worker موجود ويعمل
- [ ] Blade Template مضاف
- [ ] API Endpoint موجود
- [ ] التوكن يظهر بنجاح

### الاختبار

- [ ] الإشعار يصل بنجاح
- [ ] Sweet Alert يعمل
- [ ] إشعار النظام يعمل
- [ ] لا توجد أخطاء في Logs

---

## 🎉 النجاح!

إذا تم تحديد جميع الصناديق أعلاه، فالنظام يعمل بنجاح! 🚀

### الخطوات التالية:

1. إرسال إشعارات لمستخدمين حقيقيين
2. الاشتراك في Topics
3. جدولة الإشعارات
4. تخصيص Sweet Alert
5. تتبع قراءة الإشعارات

---

## 📚 المراجع

- **دليل شامل:** `COMPLETE_SETUP_GUIDE_AR.md`
- **دليل التثبيت:** `INSTALLATION_GUIDE_AR.md`
- **الوثائق الأساسية:** `README.md`
- **حالة الـ Package:** `PACKAGE_STATUS_AR.md`

---

**الوقت المتوقع للإعداد الكامل:** 45-60 دقيقة
**مستوى الصعوبة:** متوسط
**النتيجة:** نظام إشعارات متكامل وجاهز للإنتاج! ✅
