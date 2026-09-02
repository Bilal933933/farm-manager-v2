# Task 006: إنشاء API Resources وتطبيق قاعدة التقسيم الجديدة

## القاعدة الجديدة (معتمدة)
**Controller يستدعي Action فقط، والـ Action يستدعي ما يحتاجه (Resources, Policies, Models)**
```
Request (validation) -> Controller (يستدعي Action فقط) -> Action (ينفذ المنطق + يستدعي Policy للتحقق + يرجع Resource/DTO) -> Response
```
- Controller: نحيف، لا منطق، لا DB، فقط `return $action->execute(validated)`
- Action: منسق، يحتوي Transaction، يستدعي Policies و Resources
- Resources: تحدد شكل JSON مرة واحدة

## الهدف
إنشاء 3 Resources وإعادة كتابة `AuthController` ليستخدمها عبر Actions حسب القاعدة الجديدة.

## الملفات المطلوبة
- `microservices-demo/services/auth-service/app/Http/Resources/UserResource.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Resources/CompanyResource.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Resources/AuthResource.php` (جديد)
- `microservices-demo/services/auth-service/app/Actions/Auth/RegisterAction.php` (تعديل - يرجع Resource)
- `microservices-demo/services/auth-service/app/Actions/Auth/LoginAction.php` (تعديل - يرجع Resource)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - يستخدم Actions التي ترجع Resources)

## خطوات التنفيذ

### 1. UserResource
```php
// app/Http/Resources/UserResource.php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResource extends JsonResource {
  public function toArray($request): array {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->when(!is_null($this->phone), $this->phone),
      'company_id' => $this->company_id,
      'role' => $this->whenLoaded('role', fn() => $this->role?->name),
      'is_active' => $this->when($request->user()?->can('system.view'), $this->is_active),
    ];
  }
}
```

### 2. CompanyResource
```php
// app/Http/Resources/CompanyResource.php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class CompanyResource extends JsonResource {
  public function toArray($request): array {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'plan' => $this->plan,
      'trial_ends_at' => $this->when($this->trial_ends_at, $this->trial_ends_at?->toISOString()),
    ];
  }
}
```

### 3. AuthResource
```php
// app/Http/Resources/AuthResource.php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class AuthResource extends JsonResource {
  // $this->resource = ['company'=>Company, 'user'=>User, 'role'=>Role]
  public function toArray($request): array {
    return [
      'company' => new CompanyResource($this->resource['company']),
      'user' => new UserResource($this->resource['user']),
      'role' => $this->resource['role']->name ?? $this->resource['role'],
    ];
  }
  public function with($request): array { return ['success'=>true]; }
}
```

### 4. تعديل RegisterAction - يرجع Resource
```php
// app/Actions/Auth/RegisterAction.php - تعديل return
use App\Http\Resources\AuthResource;
public function execute(array $data): AuthResource {
  $result = DB::transaction(function() use ($data) { ... return ['company'=>$company,'user'=>$user,'role'=>$managerRole]; });
  return new AuthResource($result);
}
```

### 5. تعديل AuthController - نحيف تماماً
```php
// app/Http/Controllers/AuthController.php
public function register(RegisterRequest $request, RegisterAction $action) {
  $resource = $action->execute($request->validated());
  Auth::login($resource->resource['user']);
  if ($request->hasSession()) $request->session()->regenerate();
  return $resource->response()->setStatusCode(201);
}
public function login(LoginRequest $request, LoginAction $action) {
  $resource = $action->execute($request->validated());
  Auth::login($resource->resource['user']);
  if ($request->hasSession()) $request->session()->regenerate();
  return $resource->response();
}
```

## معايير القبول
- [ ] `GET /api/auth/register` يرجع نفس JSON السابق لكن عبر `AuthResource`
- [ ] لا `array` يدوي في `AuthController` - فقط `Action->execute()->response()`
- [ ] `UserResource` يخفي `password, remember_token` تلقائياً
- [ ] `php artisan test` أو `curl` يعمل بدون تغيير في المخرجات

## ملاحظات
- حافظ على `statefulApi()` في `bootstrap/app.php`
- Action هو من يستدعي Resources/Policies، Controller لا يستدعيهم مباشرة
- استخدم `whenLoaded` للأداء
