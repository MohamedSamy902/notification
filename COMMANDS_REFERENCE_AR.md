# 🚀 مرجع الأوامر السريع - FCM Notifications Package

## أوامر التثبيت والإعداد

---

## 📦 التثبيت الأولي

### 1. نسخ الـ Package

```bash
# إنشاء مجلد packages
mkdir -p app/Packages

# نسخ الـ package
cp -r /path/to/fcm-notifications app/Packages/

# تحديث autoload
composer dump-autoload
```

### 2. نشر الملفات

```bash
# نشر كل شيء مرة واحدة
php artisan vendor:publish --provider="App\Packages\FcmNotifications\FcmNotificationServiceProvider"

# أو نشر كل شيء على حدة:
php artisan vendor:publish --tag=fcm-notifications-config
php artisan vendor:publish --tag=fcm-notifications-migrations
php artisan vendor:publish --tag=fcm-notifications-views
php artisan vendor:publish --tag=fcm-notifications-assets
```

### 3. تشغيل Migrations

```bash
php artisan migrate
```

### 4. حفظ Firebase Credentials

```bash
# نسخ الملف
cp ~/Downloads/your-firebase-key.json storage/app/firebase_credentials.json

# ضبط الصلاحيات
chmod 600 storage/app/firebase_credentials.json

# إضافة إلى .gitignore
echo "storage/app/firebase_credentials.json" >> .gitignore
```

---

## 🧪 الاختبار

### تشغيل السيرفر

```bash
php artisan serve
```

### فتح Tinker للاختبار

```bash
php artisan tinker
```

### إرسال إشعار تجريبي

```php
// في Tinker
$service = app(\App\Packages\FcmNotifications\Contracts\NotificationServiceInterface::class);

// إرسال لتوكن محدد
$service->sendToToken(
    'YOUR_FCM_TOKEN',
    'عنوان الإشعار',
    'محتوى الإشعار',
    ['type' => 'success'],
    ['image' => 'https://example.com/image.png']
);

// إرسال لمستخدم محدد
$user = \App\Models\User::find(1);
$service->sendToUser(
    $user,
    'مرحباً!',
    'هذا إشعار تجريبي',
    ['type' => 'info']
);

// إرسال لـ Topic
$service->sendToTopic(
    'news',
    'خبر عاجل',
    'محتوى الخبر',
    ['type' => 'warning']
);

// الاشتراك في Topic
$service->subscribeTokensToTopic('YOUR_TOKEN', 'news');
```

---

## 🔍 استكشاف الأخطاء

### مشاهدة Logs

```bash
# مشاهدة آخر 50 سطر
tail -n 50 storage/logs/laravel.log

# مشاهدة مباشرة (live)
tail -f storage/logs/laravel.log

# البحث عن أخطاء FCM
grep "FCM" storage/logs/laravel.log
```

### مسح الـ Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### التحقق من قاعدة البيانات

```bash
php artisan tinker
```

```php
// عرض جميع التوكنات
\App\Models\DeviceToken::all();

// عرض التوكنات النشطة فقط
\App\Models\DeviceToken::where('is_active', true)->get();

// عرض آخر 10 إشعارات
\App\Models\NotificationLog::latest()->take(10)->get();

// عدد التوكنات المحفوظة
\App\Models\DeviceToken::count();
```

---

## 📊 أوامر الصيانة

### تنظيف التوكنات القديمة

```php
// في Tinker
// حذف التوكنات غير المستخدمة منذ 30 يوم
\App\Models\DeviceToken::where('last_used_at', '<', now()->subDays(30))->delete();

// تعطيل التوكنات القديمة بدلاً من حذفها
\App\Models\DeviceToken::where('last_used_at', '<', now()->subDays(30))
    ->update(['is_active' => false]);
```

### تنظيف سجل الإشعارات

```php
// حذف الإشعارات القديمة (أكثر من 90 يوم)
\App\Models\NotificationLog::where('created_at', '<', now()->subDays(90))->delete();

// حذف الإشعارات المقروءة القديمة
\App\Models\NotificationLog::where('is_read', true)
    ->where('read_at', '<', now()->subDays(30))
    ->delete();
```

---

## 🔄 أوامر Git

### إضافة الملفات

```bash
# إضافة كل الملفات
git add .

# إضافة الـ package فقط
git add app/Packages/fcm-notifications/

# Commit
git commit -m "Add FCM Notifications Package"

# Push
git push origin main
```

### التأكد من .gitignore

```bash
# التحقق من أن firebase_credentials.json في .gitignore
cat .gitignore | grep firebase_credentials.json

# إضافته إذا لم يكن موجوداً
echo "storage/app/firebase_credentials.json" >> .gitignore
```

---

## 🧹 أوامر التنظيف

### حذف الـ Package (إذا لزم الأمر)

```bash
# حذف المجلد
rm -rf app/Packages/fcm-notifications

# التراجع عن Migrations
php artisan migrate:rollback --step=2

# مسح الـ Cache
php artisan config:clear
composer dump-autoload
```

---

## 📱 أوامر cURL للاختبار

### إرسال إشعار عبر API

```bash
# إرسال لتوكن محدد
curl -X POST http://localhost:8000/notifications/send-test \
  -H "Content-Type: application/json" \
  -d '{"token": "YOUR_FCM_TOKEN"}'

# إرسال لمستخدم محدد
curl -X POST http://localhost:8000/notifications/send-to-user \
  -H "Content-Type: application/json" \
  -d '{"user_id": 1}'

# إرسال لـ Topic
curl -X POST http://localhost:8000/notifications/send-to-topic \
  -H "Content-Type: application/json" \
  -d '{"topic": "news"}'

# الاشتراك في Topic
curl -X POST http://localhost:8000/notifications/subscribe-to-topic \
  -H "Content-Type: application/json" \
  -d '{"token": "YOUR_TOKEN", "topic": "news"}'

# عرض جميع التوكنات
curl http://localhost:8000/notifications/list-tokens
```

---

## 🔐 أوامر الأمان

### التحقق من الصلاحيات

```bash
# التحقق من صلاحيات firebase_credentials.json
ls -la storage/app/firebase_credentials.json

# يجب أن تكون: -rw------- (600)
# إذا لم تكن كذلك:
chmod 600 storage/app/firebase_credentials.json
```

### التحقق من .env

```bash
# التأكد من أن .env في .gitignore
cat .gitignore | grep .env

# عرض إعدادات Firebase (بدون القيم الحساسة)
grep "FIREBASE_" .env | sed 's/=.*/=***/'
```

---

## 📈 أوامر المراقبة

### إحصائيات قاعدة البيانات

```bash
php artisan tinker
```

```php
// عدد المستخدمين الذين لديهم توكنات
\App\Models\User::has('devices')->count();

// عدد الإشعارات المرسلة اليوم
\App\Models\NotificationLog::whereDate('created_at', today())->count();

// عدد الإشعارات غير المقروءة
\App\Models\NotificationLog::where('is_read', false)->count();

// متوسط الإشعارات لكل مستخدم
\App\Models\NotificationLog::count() / \App\Models\User::count();
```

---

## 🚀 أوامر Production

### قبل النشر

```bash
# تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache

# التأكد من البيئة
php artisan env

# التحقق من الـ Dependencies
composer install --no-dev --optimize-autoloader
```

### بعد النشر

```bash
# تشغيل Migrations
php artisan migrate --force

# مسح الـ Cache القديم
php artisan cache:clear
php artisan config:clear
```

---

## 🧪 أوامر الاختبارات

### تشغيل الاختبارات

```bash
# تشغيل جميع الاختبارات
php artisan test

# تشغيل اختبارات محددة
php artisan test --filter NotificationServiceTest

# تشغيل مع Coverage
php artisan test --coverage
```

---

## 📝 أوامر مفيدة أخرى

### إنشاء Controller جديد

```bash
php artisan make:controller NotificationController
```

### إنشاء Migration جديد

```bash
php artisan make:migration add_column_to_device_tokens
```

### إنشاء Model جديد

```bash
php artisan make:model DeviceToken -m
```

### عرض Routes

```bash
# عرض جميع Routes
php artisan route:list

# البحث عن routes معينة
php artisan route:list | grep notification
```

---

## 🔧 أوامر Composer

### تحديث Dependencies

```bash
# تحديث الكل
composer update

# تحديث package محدد
composer update guzzlehttp/guzzle

# التحقق من الإصدارات
composer show
```

---

## 💡 نصائح سريعة

### Alias مفيد

أضف إلى `~/.bashrc` أو `~/.zshrc`:

```bash
alias pa="php artisan"
alias pat="php artisan tinker"
alias pam="php artisan migrate"
alias logs="tail -f storage/logs/laravel.log"
```

ثم استخدم:

```bash
pa serve
pat
pam
logs
```

---

## 📚 المراجع السريعة

- **دليل كامل:** `COMPLETE_SETUP_GUIDE_AR.md`
- **قائمة تحقق:** `CHECKLIST_AR.md`
- **حالة Package:** `PACKAGE_STATUS_AR.md`
- **README:** `README.md`

---

**آخر تحديث:** 2025-12-04
**الإصدار:** 1.0.0
