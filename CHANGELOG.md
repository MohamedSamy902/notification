# Changelog

جميع التغييرات المهمة في هذا المشروع سيتم توثيقها في هذا الملف.

التنسيق مبني على [Keep a Changelog](https://keepachangelog.com/ar/1.0.0/)،
وهذا المشروع يتبع [Semantic Versioning](https://semver.org/lang/ar/).

---

## [2.0.0] - 2024-12-04

### 🎉 إصدار رئيسي جديد - Clean Code & SOLID Principles

هذا الإصدار يمثل إعادة بناء كاملة للـ Package مع التركيز على جودة الكود وأفضل الممارسات.

### ✨ Added (إضافات)

#### البنية المعمارية

- إضافة **Interfaces** لجميع الخدمات (Dependency Inversion Principle)
  - `FcmAuthInterface`
  - `FcmSenderInterface`
  - `NotificationServiceInterface`
- تطبيق كامل لمبادئ **SOLID** الخمسة
- استخدام **Type Hints** في كل مكان (PHP 8+)
- **Dependency Injection** كامل عبر ServiceProvider

#### التوثيق

- ✅ `INSTALLATION_GUIDE_AR.md` - دليل تثبيت واستخدام شامل بالعربية
- ✅ `SOLID_PRINCIPLES_AR.md` - شرح مفصل لمبادئ SOLID المطبقة
- ✅ تحديث `README.md` مع badges وروابط للوثائق
- ✅ إضافة أمثلة عملية للاختبار (Unit & Integration Tests)

#### الميزات

- دعم تعطيل حفظ السجلات عبر `log_to_database` في الإعدادات
- استخدام `config()` helper بدلاً من القيم الثابتة
- تحسين معالجة الأخطاء والـ Logging

### 🔄 Changed (تغييرات)

#### إعادة الهيكلة

- **Breaking Change**: تغيير Constructor signatures لاستخدام Interfaces

  ```php
  // قبل
  public function __construct(FcmSenderService $fcmSender)

  // بعد
  public function __construct(FcmSenderInterface $fcmSender)
  ```

- تحديث `FcmNotificationServiceProvider` لربط Interfaces
- تحسين بنية الـ Classes وفقاً لـ Single Responsibility Principle

#### الكود

- إضافة **return types** لجميع الدوال
- تحسين **PHPDoc** comments
- توحيد أسلوب الكتابة (Code Style)
- تحسين أسماء المتغيرات والدوال

### 🗑️ Removed (حذف)

#### تنظيف الكود

- ✅ حذف **جميع** الكود المعلق عليه (Commented Code)
- ✅ حذف الـ Imports غير المستخدمة
- ✅ حذف الدوال والمتغيرات غير المستخدمة
- ✅ حذف التعليقات الزائدة والمكررة
- ✅ حذف `dd()` و debug code

#### ملفات

- حذف الكود القديم من `NotificationService.php` (lines 86-118)
- تنظيف `FcmSenderService.php` من debug code

### 🐛 Fixed (إصلاحات)

- إصلاح return types لتتوافق مع Interfaces
- إصلاح مشاكل الـ Type Hints
- تحسين معالجة الأخطاء في `FcmAuthService`
- إصلاح مشاكل الـ Logging

### 📝 Documentation

#### دليل التثبيت الشامل

- خطوات التثبيت التفصيلية
- إعداد Firebase بالصور
- أمثلة كود كاملة
- استكشاف الأخطاء
- الأسئلة الشائعة (FAQ)

#### شرح SOLID Principles

- شرح كل مبدأ مع أمثلة من الـ Package
- أمثلة عملية للتوسع
- أمثلة Unit Tests
- مخططات توضيحية

### 🔒 Security

- التأكيد على عدم مشاركة `firebase_credentials.json`
- إضافة تعليمات `.gitignore`
- توضيح أفضل الممارسات الأمنية

### ⚡ Performance

- تحسين الـ Dependency Injection
- استخدام Singleton للخدمات
- تقليل الـ Memory Footprint

---

## [1.0.0] - 2024-12-03

### ✨ Added

#### الميزات الأساسية

- إرسال إشعارات لمستخدم محدد
- إرسال إشعارات لتوكن محدد
- إرسال إشعارات لـ Topics
- الاشتراك في Topics
- دعم الصور والروابط والأصوات
- حفظ سجل الإشعارات في قاعدة البيانات

#### Frontend

- دعم Sweet Alert 2
- دعم إشعارات النظام (System Notifications)
- Service Worker للإشعارات في الخلفية
- تكامل مع Firebase SDK

#### الإعدادات

- ملف إعدادات شامل `fcm-notifications.php`
- دعم البيئات المختلفة (Local/Production)
- إعدادات قابلة للتخصيص بالكامل

#### قاعدة البيانات

- Migration لجدول `device_tokens`
- Migration لجدول `notification_logs`
- Indexes لتحسين الأداء

#### التوثيق

- `README.md` أساسي
- `QUICK_START.md` للبدء السريع
- `FUTURE_FEATURES.md` للميزات المستقبلية

---

## Migration Guide (دليل الترقية)

### من v1.0.0 إلى v2.0.0

#### 1. تحديث الـ Imports

```php
// قبل
use App\Packages\FcmNotifications\Services\NotificationService;

// بعد (موصى به)
use App\Packages\FcmNotifications\Contracts\NotificationServiceInterface;
```

#### 2. تحديث Dependency Injection

```php
// قبل
public function __construct(NotificationService $service)

// بعد
public function __construct(NotificationServiceInterface $service)
```

#### 3. تحديث ServiceProvider (إذا كنت تستخدم custom binding)

```php
// قبل
$this->app->singleton(NotificationService::class, function ($app) {
    return new NotificationService(...);
});

// بعد
$this->app->singleton(NotificationServiceInterface::class, NotificationService::class);
```

#### 4. لا حاجة لتغيير استخدام الـ Methods

جميع الـ methods تعمل بنفس الطريقة:

```php
$service->sendToUser($user, $title, $body, $data, $options);
$service->sendToToken($token, $title, $body, $data, $options);
$service->sendToTopic($topic, $title, $body, $data, $options);
```

#### 5. تحديث الإعدادات (اختياري)

أضف هذه الإعدادات الجديدة إلى `config/fcm-notifications.php`:

```php
'log_to_database' => true,  // تحكم في حفظ السجلات
```

---

## Upcoming Features (الميزات القادمة)

راجع ملف [FUTURE_FEATURES.md](FUTURE_FEATURES.md) للميزات المخطط لها.

---

## Support (الدعم)

للمساعدة أو الإبلاغ عن مشاكل:

1. راجع [دليل التثبيت](INSTALLATION_GUIDE_AR.md)
2. راجع [شرح SOLID](SOLID_PRINCIPLES_AR.md)
3. افتح Issue على GitHub

---

## License

MIT License - راجع ملف [LICENSE](LICENSE) للتفاصيل.
