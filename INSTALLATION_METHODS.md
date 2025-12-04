# 📦 طرق التثبيت المتاحة

## FCM Notifications Package

---

## ✅ الإصدارات المتاحة

### Tags (إصدارات مستقرة)

- `v1.0.0` - الإصدار الأول
- `v1.1.0` - إصدار محدث
- `v1.2.0` - أحدث إصدار مستقر

### Branches (إصدارات التطوير)

- `dev-main` - آخر التحديثات (يتحدث تلقائياً)

---

## 🚀 طرق التثبيت

### الطريقة 1: من GitHub مباشرة (موصى بها حالياً)

#### أ. تثبيت آخر إصدار مستقر (v1.2.0)

في `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/MohamedSamy902/notification"
    }
  ],
  "require": {
    "mohamedsamy/fcm-notifications": "^1.2"
  }
}
```

ثم:

```bash
composer update
```

#### ب. تثبيت آخر التحديثات (dev-main)

في `composer.json`:

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

ثم:

```bash
composer update
```

#### ج. تثبيت إصدار محدد

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/MohamedSamy902/notification"
    }
  ],
  "require": {
    "mohamedsamy/fcm-notifications": "v1.1.0"
  }
}
```

---

### الطريقة 2: أمر واحد مباشر

#### تثبيت dev-main

```bash
composer require mohamedsamy/fcm-notifications:dev-main
```

**ملاحظة:** قد تحتاج لإضافة repository أولاً في `composer.json`

---

### الطريقة 3: من Packagist (بعد النشر)

بعد نشر الـ Package على Packagist.org، يمكن التثبيت مباشرة:

```bash
# آخر إصدار مستقر
composer require mohamedsamy/fcm-notifications

# إصدار محدد
composer require mohamedsamy/fcm-notifications:^1.2

# آخر التحديثات
composer require mohamedsamy/fcm-notifications:dev-main
```

---

## 📋 مقارنة الإصدارات

| الإصدار    | الوصف                        | متى تستخدمه           |
| ---------- | ---------------------------- | --------------------- |
| `dev-main` | آخر التحديثات من branch main | للتطوير والتجربة      |
| `^1.2`     | آخر إصدار 1.x                | للإنتاج (موصى به)     |
| `v1.2.0`   | إصدار محدد                   | عندما تريد إصدار ثابت |
| `v1.1.0`   | إصدار سابق                   | للتوافق مع كود قديم   |

---

## 🔄 التحديث

### تحديث إلى آخر إصدار

```bash
composer update mohamedsamy/fcm-notifications
```

### التبديل بين الإصدارات

**من dev-main إلى v1.2.0:**

```bash
composer require mohamedsamy/fcm-notifications:v1.2.0
```

**من v1.2.0 إلى dev-main:**

```bash
composer require mohamedsamy/fcm-notifications:dev-main
```

---

## ✅ التحقق من الإصدار المثبت

```bash
composer show mohamedsamy/fcm-notifications
```

---

## 💡 نصائح

### للتطوير

استخدم `dev-main` للحصول على آخر التحديثات:

```json
"mohamedsamy/fcm-notifications": "dev-main"
```

### للإنتاج

استخدم إصدار مستقر:

```json
"mohamedsamy/fcm-notifications": "^1.2"
```

### للاختبار

استخدم إصدار محدد:

```json
"mohamedsamy/fcm-notifications": "v1.2.0"
```

---

## 🎯 مثال كامل

ملف `composer.json` كامل:

```json
{
  "name": "mycompany/myproject",
  "description": "My Laravel Project",
  "type": "project",
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0",
    "mohamedsamy/fcm-notifications": "dev-main"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/MohamedSamy902/notification"
    }
  ],
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

ثم:

```bash
composer install
```

---

## 🔍 استكشاف الأخطاء

### المشكلة: "Could not find package"

**الحل:**
تأكد من إضافة repository في `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/MohamedSamy902/notification"
    }
]
```

### المشكلة: "Minimum stability"

**الحل:**
أضف إلى `composer.json`:

```json
"minimum-stability": "dev",
"prefer-stable": true
```

---

## 📚 بعد التثبيت

بعد التثبيت بنجاح، اتبع:

1. **[دليل الإعداد الكامل](COMPLETE_SETUP_GUIDE_AR.md)** - للإعداد من الصفر
2. **[دليل التثبيت السريع](INSTALLATION.md)** - للخطوات السريعة
3. **[README](README.md)** - للوثائق الكاملة

---

## 🔗 الروابط

- **GitHub Repository**: https://github.com/MohamedSamy902/notification
- **Latest Release**: https://github.com/MohamedSamy902/notification/releases/latest
- **All Releases**: https://github.com/MohamedSamy902/notification/releases

---

**آخر تحديث:** 2025-12-04
**الإصدار الحالي:** v1.2.0
**Branch التطوير:** dev-main ✅
