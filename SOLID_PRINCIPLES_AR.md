# مبادئ SOLID المطبقة في FCM Notifications Package

## 📚 نظرة عامة

تم بناء هذا الـ Package وفقاً لمبادئ **SOLID** الخمسة لضمان كود نظيف، قابل للصيانة، وسهل التوسع. هذا الملف يشرح كيف تم تطبيق كل مبدأ.

---

## 1️⃣ Single Responsibility Principle (SRP)

### مبدأ المسؤولية الواحدة

> **القاعدة**: كل Class يجب أن يكون له مسؤولية واحدة فقط، وسبب واحد فقط للتغيير.

### التطبيق في الـ Package:

#### ✅ `FcmAuthService`

**المسؤولية الوحيدة**: المصادقة مع Firebase

```php
class FcmAuthService implements FcmAuthInterface
{
    // مسؤول فقط عن:
    // 1. توليد Access Token
    // 2. الحصول على Project ID

    public function getAccessToken(): ?string { }
    public function getProjectId(): ?string { }
}
```

**لماذا هذا جيد؟**

- إذا تغيرت طريقة المصادقة، نعدل هذا الـ Class فقط
- سهل الاختبار (Unit Testing)
- واضح ومفهوم

#### ✅ `FcmSenderService`

**المسؤولية الوحيدة**: إرسال الرسائل إلى FCM API

```php
class FcmSenderService implements FcmSenderInterface
{
    // مسؤول فقط عن:
    // 1. إرسال الرسائل
    // 2. الاشتراك في Topics

    public function send(array $messagePayload): bool { }
    public function subscribeTokensToTopic(array $tokens, string $topic): bool { }
}
```

**لماذا هذا جيد؟**

- لا يهتم بكيفية المصادقة (يستخدم `FcmAuthService`)
- لا يهتم ببناء الرسالة (يستقبلها جاهزة)
- فقط يرسل!

#### ✅ `NotificationService`

**المسؤولية الوحيدة**: تنسيق عملية إرسال الإشعارات

```php
class NotificationService implements NotificationServiceInterface
{
    // مسؤول فقط عن:
    // 1. تنسيق عملية الإرسال
    // 2. بناء هيكل الرسالة
    // 3. حفظ السجلات (اختياري)

    public function sendToUser(User $user, ...) { }
    public function sendToToken(string $token, ...) { }
    public function sendToTopic(string $topic, ...) { }
}
```

**لماذا هذا جيد؟**

- لا يهتم بتفاصيل FCM API
- يستخدم Services أخرى للقيام بالمهام المتخصصة
- سهل التعديل والتوسع

---

## 2️⃣ Open/Closed Principle (OCP)

### مبدأ المفتوح/المغلق

> **القاعدة**: الكود يجب أن يكون مفتوحاً للتوسع، ومغلقاً للتعديل.

### التطبيق في الـ Package:

#### ✅ استخدام Interfaces

```php
// Interface (مفتوح للتوسع)
interface FcmSenderInterface
{
    public function send(array $messagePayload): bool;
}

// Implementation الحالي
class FcmSenderService implements FcmSenderInterface
{
    public function send(array $messagePayload): bool
    {
        // إرسال عبر FCM
    }
}

// يمكنك إنشاء Implementation جديد دون تعديل الكود الموجود!
class CustomFcmSender implements FcmSenderInterface
{
    public function send(array $messagePayload): bool
    {
        // طريقتك الخاصة (مثلاً: إرسال عبر خدمة أخرى)
    }
}
```

#### ✅ مثال عملي: إضافة خدمة إرسال جديدة

لنفترض أنك تريد إضافة دعم لـ **Pusher** بجانب FCM:

```php
// 1. أنشئ Implementation جديد
class PusherSenderService implements FcmSenderInterface
{
    public function send(array $messagePayload): bool
    {
        // إرسال عبر Pusher
        return Pusher::send($messagePayload);
    }

    public function subscribeTokensToTopic(array $tokens, string $topic): bool
    {
        // Pusher implementation
    }
}

// 2. غيّر الـ Binding في ServiceProvider
$this->app->singleton(FcmSenderInterface::class, PusherSenderService::class);

// 3. كل الكود الموجود سيعمل بدون تعديل! ✨
```

**لماذا هذا جيد؟**

- لا حاجة لتعديل `NotificationService`
- لا حاجة لتعديل Controllers
- فقط أضف Implementation جديد!

---

## 3️⃣ Liskov Substitution Principle (LSP)

### مبدأ استبدال ليسكوف

> **القاعدة**: يجب أن تكون قادراً على استبدال أي Class بـ Subclass دون كسر الكود.

### التطبيق في الـ Package:

#### ✅ كل Implementations تحترم العقد (Contract)

```php
// العقد (Interface)
interface FcmAuthInterface
{
    public function getAccessToken(): ?string;
    public function getProjectId(): ?string;
}

// Implementation 1: Firebase
class FcmAuthService implements FcmAuthInterface
{
    public function getAccessToken(): ?string
    {
        // يعيد string أو null (يحترم العقد)
        return $this->generateToken();
    }

    public function getProjectId(): ?string
    {
        // يعيد string أو null (يحترم العقد)
        return $this->credentials['project_id'] ?? null;
    }
}

// Implementation 2: Mock للاختبار
class MockFcmAuthService implements FcmAuthInterface
{
    public function getAccessToken(): ?string
    {
        // يعيد string أو null (يحترم العقد)
        return 'mock_token_123';
    }

    public function getProjectId(): ?string
    {
        // يعيد string أو null (يحترم العقد)
        return 'mock_project_id';
    }
}
```

#### ✅ الاستبدال يعمل بسلاسة

```php
// في الكود الأصلي
$authService = new FcmAuthService();
$token = $authService->getAccessToken();

// في الاختبارات
$authService = new MockFcmAuthService();
$token = $authService->getAccessToken();

// كلاهما يعمل بنفس الطريقة! ✅
```

**لماذا هذا جيد؟**

- سهولة الاختبار (Unit Testing)
- يمكن استبدال Implementations بسهولة
- الكود يعمل بشكل متوقع

---

## 4️⃣ Interface Segregation Principle (ISP)

### مبدأ فصل الواجهات

> **القاعدة**: لا تجبر Class على تنفيذ methods لا يحتاجها.

### التطبيق في الـ Package:

#### ✅ Interfaces صغيرة ومتخصصة

بدلاً من Interface واحد كبير:

```php
// ❌ سيء: Interface كبير
interface FcmServiceInterface
{
    public function getAccessToken(): ?string;
    public function getProjectId(): ?string;
    public function send(array $messagePayload): bool;
    public function subscribeTokensToTopic(array $tokens, string $topic): bool;
    public function sendToUser(User $user, ...): void;
    public function sendToToken(string $token, ...): bool;
    public function sendToTopic(string $topic, ...): bool;
}
```

استخدمنا Interfaces منفصلة:

```php
// ✅ جيد: Interfaces صغيرة ومتخصصة

// للمصادقة فقط
interface FcmAuthInterface
{
    public function getAccessToken(): ?string;
    public function getProjectId(): ?string;
}

// للإرسال فقط
interface FcmSenderInterface
{
    public function send(array $messagePayload): bool;
    public function subscribeTokensToTopic(array $tokens, string $topic): bool;
}

// للخدمة الرئيسية
interface NotificationServiceInterface
{
    public function sendToUser(User $user, ...): void;
    public function sendToToken(string $token, ...): bool;
    public function sendToTopic(string $topic, ...): bool;
    public function subscribeTokensToTopic(string $token, string $topic): bool;
}
```

**لماذا هذا جيد؟**

- كل Interface له غرض واحد واضح
- Classes لا تضطر لتنفيذ methods لا تحتاجها
- سهل الفهم والصيانة

#### ✅ مثال عملي

لنفترض أنك تريد خدمة للمصادقة فقط (بدون إرسال):

```php
// يمكنك تنفيذ FcmAuthInterface فقط
class SimpleFcmAuth implements FcmAuthInterface
{
    public function getAccessToken(): ?string { }
    public function getProjectId(): ?string { }

    // لا حاجة لتنفيذ send() أو subscribeTokensToTopic() ✅
}
```

---

## 5️⃣ Dependency Inversion Principle (DIP)

### مبدأ عكس الاعتماديات

> **القاعدة**: اعتمد على Abstractions (Interfaces) وليس Implementations (Classes).

### التطبيق في الـ Package:

#### ✅ Dependency Injection عبر Interfaces

```php
// ❌ سيء: الاعتماد على Class محدد
class NotificationService
{
    protected FcmSenderService $fcmSender;  // ❌ Class محدد

    public function __construct(FcmSenderService $fcmSender)
    {
        $this->fcmSender = $fcmSender;
    }
}

// ✅ جيد: الاعتماد على Interface
class NotificationService implements NotificationServiceInterface
{
    protected FcmSenderInterface $fcmSender;  // ✅ Interface
    protected FcmAuthInterface $fcmAuthService;  // ✅ Interface

    public function __construct(
        FcmSenderInterface $fcmSender,
        FcmAuthInterface $fcmAuthService
    ) {
        $this->fcmSender = $fcmSender;
        $this->fcmAuthService = $fcmAuthService;
    }
}
```

#### ✅ الربط في ServiceProvider

```php
class FcmNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ربط Interfaces بـ Implementations
        $this->app->singleton(FcmAuthInterface::class, FcmAuthService::class);
        $this->app->singleton(FcmSenderInterface::class, FcmSenderService::class);
        $this->app->singleton(NotificationServiceInterface::class, NotificationService::class);
    }
}
```

**لماذا هذا جيد؟**

1. **سهولة الاختبار**:

```php
// في الاختبارات
$this->app->singleton(FcmSenderInterface::class, MockFcmSender::class);

// الآن كل الكود يستخدم Mock بدلاً من الخدمة الحقيقية ✅
```

2. **سهولة التبديل**:

```php
// تبديل Implementation بدون تعديل الكود
$this->app->singleton(FcmSenderInterface::class, PusherSenderService::class);
```

3. **Loose Coupling** (ارتباط ضعيف):

```php
// NotificationService لا يعرف شيئاً عن FcmSenderService
// فقط يعرف FcmSenderInterface
```

---

## 📊 ملخص الفوائد

| المبدأ  | الفائدة الرئيسية         |
| ------- | ------------------------ |
| **SRP** | كود واضح وسهل الصيانة    |
| **OCP** | سهولة إضافة ميزات جديدة  |
| **LSP** | موثوقية واستقرار         |
| **ISP** | Interfaces بسيطة ومفهومة |
| **DIP** | سهولة الاختبار والتبديل  |

---

## 🧪 أمثلة عملية للاختبار

### مثال 1: Unit Test لـ NotificationService

```php
use Tests\TestCase;
use App\Packages\FcmNotifications\Contracts\FcmSenderInterface;
use App\Packages\FcmNotifications\Contracts\FcmAuthInterface;
use App\Packages\FcmNotifications\Services\NotificationService;

class NotificationServiceTest extends TestCase
{
    public function test_send_to_token()
    {
        // Mock الـ Dependencies
        $mockSender = $this->createMock(FcmSenderInterface::class);
        $mockAuth = $this->createMock(FcmAuthInterface::class);

        // تحديد السلوك المتوقع
        $mockSender->expects($this->once())
            ->method('send')
            ->willReturn(true);

        // إنشاء Service مع Mocks
        $service = new NotificationService($mockSender, $mockAuth);

        // الاختبار
        $result = $service->sendToToken('token123', 'Title', 'Body');

        $this->assertTrue($result);
    }
}
```

### مثال 2: Integration Test

```php
public function test_real_notification_sending()
{
    // استخدام Implementations الحقيقية
    $authService = app(FcmAuthInterface::class);
    $senderService = app(FcmSenderInterface::class);
    $notificationService = app(NotificationServiceInterface::class);

    $user = User::factory()->create();

    // إرسال حقيقي
    $notificationService->sendToUser($user, 'Test', 'Message');

    // التحقق
    $this->assertDatabaseHas('notification_logs', [
        'user_id' => $user->id,
        'title' => 'Test',
    ]);
}
```

---

## 🎯 الخلاصة

تطبيق مبادئ SOLID في هذا الـ Package يوفر:

1. ✅ **كود نظيف**: سهل القراءة والفهم
2. ✅ **سهولة الصيانة**: كل Class له مسؤولية واحدة
3. ✅ **قابلية التوسع**: يمكن إضافة ميزات جديدة بسهولة
4. ✅ **سهولة الاختبار**: Dependency Injection كامل
5. ✅ **مرونة**: يمكن استبدال Implementations بسهولة

---

**تذكر**: مبادئ SOLID ليست قواعد صارمة، بل إرشادات لكتابة كود أفضل. استخدمها بحكمة! 🚀
